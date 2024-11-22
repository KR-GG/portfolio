from django.db import models
import uuid

# Create your models here.
class User(models.Model):
    user_id = models.AutoField(primary_key=True)
    client_id = models.UUIDField(default=uuid.uuid4, editable=False, unique=True) # UUID or OAuth2.0 token(max_length=255로 수정 필요)

    def __str__(self):
        return self.client_id

class Device(models.Model):
    device_id = models.AutoField(primary_key=True)
    iot_id = models.UUIDField(default=uuid.uuid4, editable=False, unique=True)  # NOT NULL UNIQUE
    user = models.ForeignKey(User, on_delete=models.CASCADE)  # Foreign Key

    def __str__(self):
        return self.iot_id
    
# class Sensor(models.Model):
#     id = models.AutoField(primary_key=True)
#     iot_id = models.ForeignKey(Devices, on_delete=models.CASCADE)  # Foreign Key
#     time = models.DateTimeField()  # TIMESTAMP
#     noise = models.FloatField()  # FLOAT
#     light = models.IntegerField()  # INT
#     humidity = models.FloatField()  # FLOAT

class SleepRecord(models.Model):
    id = models.AutoField(primary_key=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE, to_field='client_id')  # Foreign Key
    start_time = models.DateTimeField()
    end_time = models.DateTimeField()
    SLEEP_STAGE_CHOICES = [
        (0, 'Unknown'),
        (1, 'Awake'),
        (2, 'Sleeping'),
        (3, 'Out_of_bed'),
        (4, 'Light'),
        (5, 'Deep'),
        (6, 'Rem'),
    ]
    sleep_stage = models.IntegerField(choices=SLEEP_STAGE_CHOICES)

    def __str__(self):
        return f"{self.user.client_id} - {self.start_time}"
    
    class Meta:
        ordering = ['-start_time']

class GPTAnalysis(models.Model):
    id = models.AutoField(primary_key=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE, to_field='client_id', default=1)  # Foreign Key
    start_time = models.DateTimeField()
    analysis = models.TextField()
    score = models.IntegerField()  # INT

    def __str__(self):
        return f"{self.user.client_id} - {self.start_time} - Score: {self.score}"
    
    class Meta:
        ordering= ['-start_time']