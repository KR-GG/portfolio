# Assembly Language Learning Directory

이 디렉토리는 어셈블리어 학습을 위한 다양한 예제와 파일들을 포함하고 있습니다. 각 디렉토리는 특정한 주제나 기능을 다루며, 관련된 소스 코드와 바이너리 파일들을 포함하고 있습니다.

## 디렉토리 설명

- **arr/**: 배열 관련 예제.
- **bin/**: 2진수 변환 예제.
- **crushed/**: 중간에 끼인 함수(stack 활용) 예제.
- **crushed-reverse/**: `crushed` 디렉토리의 디어셈블리 결과.
- **echo/**: 에코 프로그램 예제.
- **func/**: 함수 호출 관련 예제.
- **hello/**: "Hello, World!" 프로그램 예제.
- **middle/**: 중간 시험.
- **pw/**: 비밀번호 인코딩 예제.
- **reverse/**: 디어셈블리 결과.
- **swap/**: 스왑 관련 예제.

## 파일 설명

- `.s` 파일: 어셈블리어 소스 코드 파일.
- `.c` 파일: C 언어 소스 코드 파일.
- `.o` 파일: 오브젝트 파일.
- 기타 실행 파일: 컴파일된 실행 파일.

## 학습 내용

이 디렉토리의 예제들을 통해 다음과 같은 어셈블리어 프로그래밍 개념을 학습할 수 있습니다:

1. **기본 어셈블리어 문법**: 어셈블리어 명령어와 레지스터 사용법을 익힐 수 있습니다.
2. **함수 호출 및 매개변수 전달**: 어셈블리어에서 함수 호출과 매개변수 전달 방법을 학습할 수 있습니다.
   - 예제: [crushed/crushed.s](crushed/crushed.s), [crushed-reverse/crushed.s](crushed-reverse/crushed.s)
3. **조건문과 반복문**: 조건문과 반복문을 어셈블리어로 구현하는 방법을 배울 수 있습니다.
   - 예제: [middle/binary/binary.s](middle/binary/binary.s)
4. **문자열 처리**: 문자열을 처리하고 변환하는 방법을 학습할 수 있습니다.
   - 예제: [func/cap.s](func/cap.s), [func/cap_a.s](func/cap_a.s)
5. **시스템 호출**: 시스템 호출을 통해 입출력 작업을 수행하는 방법을 배울 수 있습니다.
   - 예제: [hello/hello.s](hello/hello.s), [echo/echo.s](echo/echo.s)
6. **배열 처리**: 배열의 요소를 접근하고 조작하는 방법을 학습할 수 있습니다.
   - 예제: [arr/arr.s](arr/arr.s), [middle/element/get_array_elem.s](middle/element/get_array_elem.s)
7. **비트 연산**: 비트 연산을 통해 숫자를 이진수 문자열로 변환하는 방법을 배울 수 있습니다.
   - 예제: [bin/binary.s](bin/binary.s)
