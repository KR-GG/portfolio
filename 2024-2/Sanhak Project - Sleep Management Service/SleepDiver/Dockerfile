FROM python:3.12
WORKDIR /app
COPY . /app/

RUN apt-get update && \
    apt-get install -y python3-dev default-libmysqlclient-dev gcc
      
# Install dependencies
RUN pip install -r requirements.txt

# Create staticfiles directory and set permissions
RUN mkdir -p /app/staticfiles && chmod 755 /app/staticfiles

# Collect static files will be executed in the container startup

CMD ["sh", "-c", "python3 manage.py collectstatic --noinput && gunicorn -w 4 -b 0.0.0.0:8000 SleepDiver.wsgi:application"]