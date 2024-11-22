import json
import boto3
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from bs4 import BeautifulSoup
import time
import logging
import os
import re

logger = logging.getLogger()
logger.setLevel(logging.INFO)

dynamodb = boto3.resource('dynamodb')
user_table = dynamodb.Table('User')
assign_table = dynamodb.Table('Assignment')
notice_table = dynamodb.Table('Notice')

def setup_chrome():
    """Setup Chrome with binary locations"""
    # Set complete library path
    os.environ['LD_LIBRARY_PATH'] = '/opt/chrome:/opt/chrome/lib'
    
    try:
        chrome_options = webdriver.ChromeOptions()
        chrome_options.binary_location = "/opt/chrome/chrome"
        chrome_options.add_argument("--headless")
        chrome_options.add_argument('--no-sandbox')
        chrome_options.add_argument("--single-process")
        chrome_options.add_argument("--disable-dev-shm-usage")
        chrome_options.add_argument("user-agent=Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko")
        chrome_options.add_argument('window-size=1392x1150')
        chrome_options.add_argument("disable-gpu")

        service = Service("/opt/chromedriver")
        return webdriver.Chrome(service=service, options=chrome_options)
        
    except Exception as e:
        logger.error(f"Chrome setup failed: {str(e)}")
        logger.error(f"LD_LIBRARY_PATH: {os.environ.get('LD_LIBRARY_PATH')}")
        # Add more detailed error logging
        if os.path.exists('/tmp/chromedriver.log'):
            with open('/tmp/chromedriver.log', 'r') as f:
                logger.error(f"ChromeDriver logs: {f.read()}")
        raise

def handler(event, context):
    logger.info("Starting Lambda function")
    driver = None
    
    try:
        body = json.loads(event['body'])
        user_id = body['user_id']
        password = body['password']
        logger.info(f"Processing request for user: {user_id}")

        logger.info("Setting up Chrome...")
        driver = setup_chrome()
        logger.info("Chrome initialized successfully")

        driver.get('https://lms.kau.ac.kr/login.php')
        logger.info("Navigated to login page")

        username = driver.find_element(By.NAME, 'username')
        password_field = driver.find_element(By.NAME, 'password')
        username.send_keys(user_id)
        password_field.send_keys(password)
        password_field.send_keys(Keys.RETURN)
        logger.info("Login submitted")

        time.sleep(5)

        driver.get('https://lms.kau.ac.kr/')
        logger.info("Navigated to main page")
        
        soup = BeautifulSoup(driver.page_source, 'html.parser')
        courses = soup.find_all('div', class_='course-title')
        link_tags = soup.find_all('a', class_='course_link')
        links = [tag.get('href') for tag in link_tags]
        course_pairs = list(zip(courses, links))
        
        logger.info(f"Found {len(course_pairs)} courses")

        for course in course_pairs:
            course_name_element = course[0].find('h3')
            span_new = course_name_element.find('span', class_='new')
            if span_new:
                span_new.decompose()
            course_name = course_name_element.text.strip()
            course_prof = course[0].find('p', class_='prof').text.strip()
            course_link = course[1]

            if not re.search(r'\d{4}\)$', course_name):
                logger.info(f"Skipping course: {course_name}")
                continue

            user_table.put_item(
                Item={
                    'user_id': user_id,
                    'course': course_name,
                    'prof': course_prof,
                    'url': course_link
                }
            )
            logger.info(f"Stored course: {course_name}")

            driver.get(course_link)
            logger.info(f"Visited course: {course_name}")
            time.sleep(2)

            soup = BeautifulSoup(driver.page_source, 'html.parser')
            assignments = soup.select('a:has(img[alt="과제"])')
            notice_link_element = soup.select('a:has(img[src="https://lms.kau.ac.kr/theme/image.php/coursemosv2/local_ubion/1706233720/course_format/mod_icon/ubboard_notice"])')
            notice_link = notice_link_element[0]['href'] if notice_link_element else None

            if assignments:
                for assginment in assignments:
                    assign_link = assginment['href']
                    assign_id = int(assginment['href'].split('=')[-1])
                    assign_title = assginment.find('span', class_='instancename').text
                    driver.get(assign_link)
                    logger.info(f"Visited assignment: {assign_title}")
                    time.sleep(2)

                    soup = BeautifulSoup(driver.page_source, 'html.parser')
                    intro_div = soup.find('div', id='intro')
                    assign_content = []
                    if intro_div is None:
                        logger.info(f"No content found for assignment: {assign_title}")
                        continue
                    for h4 in intro_div.find_all('h4'):
                        assign_content.append(h4.get_text(strip=True))
                    for ol in intro_div.find_all('ol'):
                        for i, li in enumerate(ol.find_all('li'), start=1):
                            text = li.get_text(separator=" ", strip=True)
                            assign_content.append(f"{i}. {text}")
                    for ul in intro_div.find_all('ul'):
                        for li in ul.find_all('li'):
                            text = li.get_text(separator=" ", strip=True)
                            assign_content.append(f"- {text}")
                    for p in intro_div.find_all('p'):
                        text = p.get_text(separator=" ", strip=True)
                        if text:
                            assign_content.append(text)
                    for pre in intro_div.find_all('pre'):
                        text = pre.get_text(separator=" ", strip=True)
                        if text:
                            assign_content.append(text)
                    assign_content = '\n'.join(assign_content)

                    assign_end = None
                    row = soup.find('td', text='종료 일시')
                    if row:
                        assign_end = row.find_next_sibling('td').get_text(strip=True)

                    assign_table.put_item(
                        Item={
                            'course': course_name,
                            'assign_id': assign_id,
                            'title': assign_title,
                            'content': assign_content,
                            'end': assign_end
                        }
                    )
                    logger.info(f"Stored assignment: {assign_title}")

            if notice_link:
                driver.get(notice_link)
                logger.info(f"Visited notice board: {course_name}")
                time.sleep(2)

                soup = BeautifulSoup(driver.page_source, 'html.parser')
                no_notice_message = soup.find('td', text='등록된 게시글이 없습니다.')
                if no_notice_message:
                    logger.info("No notices found")
                    continue

                rows = soup.select('tbody tr')

                for row in rows:
                    adding_value=0
                    columns = row.find_all('td')
                    checkbox = columns[0].find('input')
                    if checkbox:
                        adding_value=1
                    notice_id = int(columns[0+adding_value].text.strip())
                    title = columns[1+adding_value].find('a').text.strip()
                    link = columns[1+adding_value].find('a')['href']
                    author = columns[2+adding_value].text.strip()
                    date = columns[3+adding_value].text.strip()
                    driver.get(link)
                    logger.info(f"Visited notice: {title}")
                    time.sleep(2)

                    soup = BeautifulSoup(driver.page_source, 'html.parser')
                    content_div = soup.find('div', class_='text_to_html')
                    content = content_div.get_text(separator='\n').strip() if content_div else ''

                    notice_table.put_item(
                        Item={
                            'course': course_name,
                            'notice_id': notice_id,
                            'title': title,
                            'author': author,
                            'content': content,
                            'date': date
                        }
                    )
                    logger.info(f"Stored notice: {title}")

    except Exception as e:
        logger.error(f"Error occurred: {str(e)}")
        return {
            'statusCode': 500,
            'body': json.dumps({'error': str(e)})
        }
    finally:
        if driver:
            driver.quit()
            logger.info("Chrome closed")

    return {
        'statusCode': 200,
        'body': json.dumps('Success')
    }

if __name__ == '__main__':
    # Example event for local testing
    event = {
        'body': json.dumps({
            'user_id': 'your_user_id',
            'password': 'your_password'
        })
    }
    context = None
    print(handler(event, context))