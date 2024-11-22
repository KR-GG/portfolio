from django.urls import path
from drf_yasg.views import get_schema_view
from drf_yasg import openapi
from rest_framework import permissions
from .views import sleep_data_view, analysis_data_view, receive_sleep_data, sleep_score_api, sleep_analysis_api, sleep_stage_api
from django.conf import settings
from django.conf.urls.static import static

schema_view = get_schema_view(
   openapi.Info(
      title="SleepDiver API",
      default_version='v1',
      description="API description",
      terms_of_service="https://www.google.com/policies/terms/",
    #   contact=openapi.Contact(email="contact@yourapi.local"),
    #   license=openapi.License(name="BSD License"),
   ),
   public=True,
   permission_classes=(permissions.AllowAny,),
)

urlpatterns = [
    path('sleep-data-view/', sleep_data_view, name='sleep_data_view'),
    path('analysis-data-view/', analysis_data_view, name='analysis_data_view'),
    path('sleep_data/', receive_sleep_data, name='receive_sleep_data'),
    path('score/', sleep_score_api, name='sleep_score'),
    path('analysis/', sleep_analysis_api, name='sleep_analysis'),
    path('sleep-stage/', sleep_stage_api, name='sleep_stage'),

    path('swagger/', schema_view.with_ui('swagger', cache_timeout=0), name='schema-swagger-ui'),
    path('redoc/', schema_view.with_ui('redoc', cache_timeout=0), name='schema-redoc'),
] + static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
