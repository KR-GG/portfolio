from django.contrib import admin
from .models import User, Device, SleepRecord, GPTAnalysis

# Register your models here.
admin.site.register(User)
admin.site.register(Device)
admin.site.register(SleepRecord)
admin.site.register(GPTAnalysis)