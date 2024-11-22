from .models import SleepRecords, Users
from openai import OpenAI
import json



def GPT(*data):
    openai_api_key=''

    client = OpenAI(api_key=openai_api_key)

    completion = client.chat.completions.create(
        model="gpt-4o-mini",
        max_tokens=500,
        messages=[

            #GPT 모델 동작 명령
            {"role": "system", 
             "content": 
                    "You're a doctor who analyzes sleep data, \
                    The sleep data will be given in json form, \
                    Sleep data will be given in a total of five stages, \
                    Make sure the depth of your sleep is repeated normally, \
                    If abnormal patterns are found, use noise, illumination, and humidity data to analyze why, \
                    Analyzing sleep data in a professional tone \
                    "},

            #모델 수면 class 분류
            {"role": "system", 
             "content": 
                    "SleepStageRecord::class - 0: unknown, 1: awake, 2: sleeping, 3: out of bed, 4: light, 5: deep, 6: rem, 7: awake in bed \
                    "},

            #data 입력
            {
                "role": "user", 
                "content": f"sleep info : {data}"
            }
        ],

        #json 형태로 데이터 출력
        response_format={
            "type": "json_schema",
            "json_schema": {
                "name": "data_analyz_schema",
                "schema": {
                    "type": "object",
                    "properties": {
                        "Analysis": {
                            "description": "analyze the time and cause of the interruption in your sleep in Korean",
                            "type": "string"
                        },
                        "Score": {
                            "description": "Score representing the quality of sleep, Max Score is 100",
                            "type": "number"
                        }
                    },
                    "additionalProperties": False
                }
            }
        }
    )
    
    return json.loads(completion.choices[0].message.content)
