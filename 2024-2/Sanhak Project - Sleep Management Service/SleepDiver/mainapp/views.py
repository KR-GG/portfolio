from datetime import datetime, timezone, timedelta
from django.shortcuts import render
from rest_framework import status
from rest_framework.decorators import api_view
from django.http import JsonResponse
from django.shortcuts import get_object_or_404
from rest_framework.response import Response
from drf_yasg.utils import swagger_auto_schema
from drf_yasg import openapi
from .models import SleepRecord, User, GPTAnalysis
# from mainapp.gpt import GPT
import json
import os
import sqlite3
import random
from django.conf import settings

# 시간대 설정 UTC+9
utc_plus_9 = timezone(timedelta(hours=9))

@swagger_auto_schema(
    method='post',
    operation_description="수면 데이터를 수신하여 저장합니다.",
    operation_summary="수면 데이터 수신",
    manual_parameters=[
        openapi.Parameter(
            'client_uuid',
            openapi.IN_QUERY,
            description="사용자의 고유 ID (UUID 형식)",
            type=openapi.TYPE_STRING,
            required=True
        )
    ],
    request_body=openapi.Schema(
        type=openapi.TYPE_OBJECT,
        required=['sleep_data'],
        properties={
            'sleep_data': openapi.Schema(
                type=openapi.TYPE_ARRAY,
                items=openapi.Schema(
                    type=openapi.TYPE_OBJECT,
                    properties={
                        'startTime': openapi.Schema(
                            type=openapi.TYPE_INTEGER,
                            description="수면 시작 시간 (UNIX 타임스탬프)"
                        ),
                        'endTime': openapi.Schema(
                            type=openapi.TYPE_INTEGER,
                            description="수면 종료 시간 (UNIX 타임스탬프)"
                        ),
                        'stage': openapi.Schema(
                            type=openapi.TYPE_INTEGER,
                            description="수면 단계"
                        ),
                    },
                    required=['startTime', 'endTime', 'stage']
                ),
                description="수면 데이터 배열"
            )
        }
    ),
    responses={
        201: openapi.Response(description="Sleep Data received successfully."),
        400: openapi.Response(description="Bad request: 유효하지 않은 client_uuid 또는 sleep_data")
    }
)
@api_view(['POST'])
def receive_sleep_data(request):
    if request.method == 'POST':
        # 쿼리 파라미터에서 client_uuid 가져오기
        client_uuid = request.GET.get('client_uuid')
        if not client_uuid:
            return JsonResponse({'error': 'client_uuid is required'}, status=400)

        # POST 바디에서 수면 데이터 가져오기
        try:
            sleep_data = json.loads(request.body)
            if not sleep_data:
                return JsonResponse({'error': 'No sleep data provided'}, status=400)
        except json.JSONDecodeError:
            return JsonResponse({'error': 'Invalid JSON format'}, status=400)
        
        # 사용자 확인 또는 생성
        user, _ = User.objects.get_or_create(client_id=client_uuid)

        # 수면 데이터 저장
        sleep_records = [
            SleepRecord(
                user=user,
                start_time=datetime.fromtimestamp(record['startTime'], utc_plus_9),
                end_time=datetime.fromtimestamp(record['endTime'], utc_plus_9),
                sleep_stage=record['stage']
            )
            for record in sleep_data
        ]
        SleepRecord.objects.bulk_create(sleep_records)

        #gpt_result = GPT(sleep_data)
        ananlysis_options = ["너 잘 잠", "너 못 잠"]
        random_index = random.randint(0, 1)
        
        analysis_record = GPTAnalysis(
            user=user,
            start_time=datetime.fromtimestamp(sleep_data[0]['startTime'], utc_plus_9),
            #analysis=gpt_result["Analysis"],
            #score=gpt_result["Score"],
            analysis=ananlysis_options[random_index],
            score=random.randint(0, 100),
        )
        analysis_record.save()

        return Response({"message": "Sleep Data received successfully."}, status=status.HTTP_201_CREATED)

@swagger_auto_schema(
    method='get',
    operation_description="특정 사용자의 수면 시작 시간에 따른 수면 점수를 조회합니다.",
    operation_summary="수면 점수 조회",
    manual_parameters=[
        openapi.Parameter(
            'client_uuid',
            openapi.IN_QUERY,
            description="사용자의 고유 ID (UUID 형식)",
            type=openapi.TYPE_STRING,
            required=True
        ),
        openapi.Parameter(
            'sleep_start_time',
            openapi.IN_QUERY,
            description="수면 시작 시간 (UNIX 타임스탬프)",
            type=openapi.TYPE_STRING,
            required=True
        ),
    ],
    responses={
        200: openapi.Response(
            description="수면 점수가 성공적으로 반환되었습니다.",
            examples={
                "application/json": {
                    "score": 85
                }
            }
        ),
        400: openapi.Response(
            description="요청이 잘못되었습니다. 필수 파라미터 누락 또는 잘못된 시간 형식.",
            examples={
                "application/json": {
                    "error": "client_uuid and sleep_start_time are required."
                }
            }
        ),
        404: openapi.Response(
            description="해당하는 사용자의 기록이 없습니다.",
            examples={
                "application/json": {
                    "error": "User or GPTAnalysis record not found."
                }
            }
        )
    }
)
@api_view(['GET'])
def sleep_score_api(request):
    
    client_uuid = request.GET.get('client_uuid')
    sleep_start_time = request.GET.get('sleep_start_time')
 
    if not client_uuid or not sleep_start_time:
        return JsonResponse({'error': 'client_uuid and sleep_start_time are required.'}, status=400)
    
    try:
        user = get_object_or_404(User, client_id=client_uuid)

        sleep_start_time_dt = datetime.fromtimestamp(int(sleep_start_time), utc_plus_9)

        analysis = get_object_or_404(GPTAnalysis, user=user, start_time=sleep_start_time_dt)
    
        return JsonResponse({'score': analysis.score}, status=200)
    
    except ValueError:
        return JsonResponse({'error': 'Invalid sleep_start_time.'}, status=400)
    
@swagger_auto_schema(
    method='get',
    operation_description="특정 사용자의 수면 시작 시간에 따른 수면 분석을 조회합니다.",
    operation_summary="수면 분석 조회",
    manual_parameters=[
        openapi.Parameter(
            'client_uuid',
            openapi.IN_QUERY,
            description="사용자의 고유 ID (UUID 형식)",
            type=openapi.TYPE_STRING,
            required=True
        ),
        openapi.Parameter(
            'sleep_start_time',
            openapi.IN_QUERY,
            description="수면 시작 시간 (UNIX 타임스탬프)",
            type=openapi.TYPE_STRING,
            required=True
        ),
    ],
    responses={
        200: openapi.Response(
            description="수면 분석이 성공적으로 반환되었습니다.",
            examples={
                "application/json": {
                    "analysis": "너 잘 잠"
                }
            }
        ),
        400: openapi.Response(
            description="요청이 잘못되었습니다. 필수 파라미터 누락 또는 잘못된 시간 형식.",
            examples={
                "application/json": {
                    "error": "client_uuid and sleep_start_time are required."
                }
            }
        ),
        404: openapi.Response(
            description="해당하는 사용자의 기록이 없습니다.",
            examples={
                "application/json": {
                    "error": "User or GPTAnalysis record not found."
                }
            }
        )
    }
)
@api_view(['GET'])
def sleep_analysis_api(request):
    # 쿼리 파라미터에서 client_uuid와 sleep_start_time 가져오기
    client_uuid = request.GET.get('client_uuid')
    sleep_start_time = request.GET.get('sleep_start_time')

    # 필수 파라미터가 없을 경우 에러 응답
    if not client_uuid or not sleep_start_time:
        return JsonResponse({'error': 'client_uuid and sleep_start_time are required.'}, status=400)
    
    try:
        # 사용자 확인 (client_uuid 기반으로 User 조회)
        user = get_object_or_404(User, client_id=client_uuid)
        
        # sleep_start_time을 DateTime 형식으로 변환
        sleep_start_time_dt = datetime.fromtimestamp(int(sleep_start_time), utc_plus_9)
        
        # start_time에 해당하는 GPTAnalysis 조회
        analysis = get_object_or_404(GPTAnalysis, user=user, start_time=sleep_start_time_dt)
        
        # analysis 반환
        return JsonResponse({'analysis': analysis.analysis}, status=200)
    
    except ValueError:
        # sleep_start_time이 올바른 형식이 아닐 경우 에러 응답
        return JsonResponse({'error': 'Invalid sleep_start_time.'}, status=400)

@swagger_auto_schema(
    method='get',
    operation_description="특정 사용자의 수면 시작 시간 이후 연속된 수면 단계를 조회합니다. 특정 단계가 5분 이상 지속되면 해당 시점까지의 데이터를 반환합니다.",
    operation_summary="수면 단계 조회",
    manual_parameters=[
        openapi.Parameter(
            'client_uuid',
            openapi.IN_QUERY,
            description="사용자의 고유 ID (UUID 형식)",
            type=openapi.TYPE_STRING,
            required=True
        ),
        openapi.Parameter(
            'sleep_start_time',
            openapi.IN_QUERY,
            description="조회할 수면 시작 시간 (UNIX 타임스탬프)",
            type=openapi.TYPE_STRING,
            required=True
        ),
    ],
    responses={
        200: openapi.Response(
            description="연속된 수면 단계가 성공적으로 반환되었습니다.",
            examples={
                "application/json": [
                    {
                        "start_time": "2023-10-10T22:00:00",
                        "end_time": "2023-10-10T22:30:00",
                        "stage": 2
                    },
                    {
                        "start_time": "2023-10-10T22:30:00",
                        "end_time": "2023-10-10T23:00:00",
                        "stage": 1
                    }
                ]
            }
        ),
        400: openapi.Response(
            description="요청이 잘못되었습니다. 필수 파라미터 누락 또는 잘못된 시간 형식.",
            examples={
                "application/json": {
                    "error": "client_uuid and sleep_start_time are required"
                }
            }
        ),
        404: openapi.Response(
            description="해당 사용자의 데이터가 없거나 사용자 미존재.",
            examples={
                "application/json": {
                    "error": "User not found"
                }
            }
        )
    }
)
@api_view(['GET'])
def sleep_stage_api(request):
    client_uuid = request.GET.get('client_uuid')
    sleep_start_time = request.GET.get('sleep_start_time')

    # 필수 파라미터 검증
    if not client_uuid or not sleep_start_time:
        return Response({"error": "client_uuid and sleep_start_time are required"}, status=400)

    try:
        # sleep_start_time을 datetime 형식으로 변환
        sleep_start_time_dt = datetime.fromtimestamp(int(sleep_start_time), utc_plus_9)
        
        # User 조회
        user = User.objects.get(client_id=client_uuid)

        # sleep_start_time 이후의 연속된 sleep_data 조회
        sleep_data = SleepRecord.objects.filter(
            user=user,
            start_time__gte=sleep_start_time_dt
        ).order_by('start_time')

        sleep_data_list = []
        
        for record in sleep_data:
            # stage가 1이고 5분 이상 지속되는 경우 중단
            if record.stage == 1 and (record.end_time - record.start_time).total_seconds() >= 300:
                break

            # sleep_data_list에 데이터 추가
            sleep_data_list.append({
                "start_time": record.start_time,
                "end_time": record.end_time,
                "stage": record.stage
            })

        return Response(sleep_data_list, status=200)

    except ValueError:
        # sleep_start_time 형식 오류
        return Response({"error": "Invalid sleep_start_time."}, status=400)

    except User.DoesNotExist:
        # 존재하지 않는 사용자 오류
        return Response({"error": "User not found"}, status=404)

    except SleepRecord.DoesNotExist:
        # 수면 데이터가 없을 경우
        return Response({"error": "No sleep data found"}, status=404)

# https://sleep-diver.com/sleep-data-view/
# SleepRecords 출력
@swagger_auto_schema(
    method='get',
    operation_description="모든 수면 기록을 조회합니다.",
    operation_summary="수면 기록 웹페이지",
    responses={
        200: "Success"
    }
)
@api_view(['GET'])
def sleep_data_view(request):
    sleep_data = SleepRecord.objects.all()  # 모든 SleepRecords 데이터 가져오기
    # 데이터 변환
    formatted_data = [
        {
            'id': record.id,
            'client_id': record.user.client_id,  # ForeignKey에서 client_id 가져오기
            'start_time': record.start_time,
            'end_time': record.end_time,
            'stage': record.sleep_stage,
        }
        for record in sleep_data
    ]
    
    return render(request, 'sleep-data-view.html', {'sleep_data': formatted_data})

# https://sleep-diver.com/analysis-data-view/
# GPTAnalysis 출력
@swagger_auto_schema(
    method='get',
    operation_description="모든 수면 분석 결과를 조회합니다.",
    operation_summary="수면 분석 결과 웹페이지",
    responses={
        200: "Success"
    }
)
@api_view(['GET'])
def analysis_data_view(request):
    analysis_data = GPTAnalysis.objects.all()  # 모든 GPTAnalysis 데이터 가져오기
    # 데이터 변환
    formatted_data = [
        {
            'id': record.id,
            'client_id': record.user.client_id,  # ForeignKey에서 client_id 가져오기
            'start_time': record.start_time,
            'analysis': record.analysis,
            'score': record.score,
        }
        for record in analysis_data
    ]
    
    return render(request, 'analysis-data-view.html', {'analysis_data': formatted_data})
