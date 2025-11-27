# 2025 데이터베이스 ESQL 텀 프로젝트: 통합 사진 관리 시스템
2025-DataBase_ESQL_TermProject

> USERS(사용자) – ALBUMS(앨범) – PHOTOS(사진) – DETAILS(사진 상세정보) 4-table join 데이터베이스를 기반으로 
> sql의 INSERT, UPDATE, DELETE 구문을 Web과 HLL 상에서의 구현 및 상호 연결을 확인하는 **통합 사진 관리 시스템**을 개발하였습니다.    

---

## 스키마 설계
- **ER-Diagram**
<p align="center>
    <img width="539" height="394" alt="image" src="https://github.com/user-attachments/assets/fa9f2f92-c13e-4c70-9981-024a993e2afe" />
</p>

---

## 핵심 기능(HLL-python)
- **view_joined_data**: 4개의 테이블의 데이터를 조회
- **insert_sample_data**: USERS, ALBUMS, PHOTOS, DETAILS 4개 테이블에 샘플 데이터를 삽입
- **update_photo_caption**: 특정 PHOTOS의 DETAILS 중 캡션을 수정
- **delete_photo_by_id**: 특정 PHOTO_ID를 가진 사진 및 관련 상세정보를 삭제

---

## 핵심 기능(Web-php)
- **4-table join 전체 결과**: 4개의 테이블 innerjoin한 결과 출력  
- **앨범별 상세 사진 조회**: 전체 앨범 목록 및 앨범 클릭 시 앨범에 있는 사진과 상세정보 출   
- **데이터 삽입 (INSERT)**:  **새로운 사용자**, **앨범**, **사진**, **상세정보**를 4개 테이블에 동시 삽입  
- **데이터 수정 (UPDATE)**: 기존 사진의 **PHOTO_ID**를 지정하여 **해상도** 정보를 수정
- **데이터 삭제 (DELETE)**: 앨범 ID를 지정하여 앨범을 삭제    

---

## 실행 화면
- **HLL(.py)**
<p align="center">
    <img width="347" height="291" alt="image" src="https://github.com/user-attachments/assets/76ac9c21-bfba-4f51-a454-968037f54163" />
    <img width="339" height="224" alt="image" src="https://github.com/user-attachments/assets/f827856b-65bb-4790-9dee-9cabec344c23" />
</p>

- **Web(.php)**
<p align="center">
    <img width="433" height="331" alt="image" src="https://github.com/user-attachments/assets/dbb26786-fd3c-4f24-82b7-955d603e059f" />
    <img width="433" height="331" alt="image" src="https://github.com/user-attachments/assets/73ac4b0c-7265-484d-b7d2-2b1331dd4abc" />
    <img width="424" height="211" alt="image" src="https://github.com/user-attachments/assets/fad04345-5641-4fa0-8f1c-d841a041ac50" />
    <img width="424" height="211" alt="image" src="https://github.com/user-attachments/assets/ada295bb-60df-4bf0-83d0-685f45e0b0cd" />
    <img width="424" height="211" alt="image" src="https://github.com/user-attachments/assets/40f3984b-e4a7-41b2-ba89-4cc28f201c5f" />
</p>

---

## 개발 환경
- **데이터베이스 시스템**: ![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
- **프로그래밍 언어**: ![Python](https://img.shields.io/badge/python-3670A0?style=for-the-badge&logo=python&logoColor=ffdd54) ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) ![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
- **웹 서버**: ![Apache](https://img.shields.io/badge/apache-%23D42029.svg?style=for-the-badge&logo=apache&logoColor=white)
- **개발 툴**: ![Visual Studio Code](https://img.shields.io/badge/Visual%20Studio%20Code-0078d7.svg?style=for-the-badge&logo=visual-studio-code&logoColor=white)
- **운영 체제**: ![Windows 11](https://img.shields.io/badge/Windows%2011-%230079d5.svg?style=for-the-badge&logo=Windows%2011&logoColor=white)

---

## 프로젝트 진행 (How to Run)

1.  **데이터베이스 구축 및 데이터 로드:** `PHOTOSYSETEM.sql` 파일 실행하여 데이터베이스 스키마를 생성 및 초기 데이터 삽입
2.  **HLL-> Web 상호 검증 확인:** `db_config.py`파일 컴파일하고 나머지 .py 파일 실행 후. 'http://localhost/photoweb.php' 의 4-table join 전체 결과 버튼 눌러 결과 조회
3.  **Web -> HLL 상호 검증 확인:** apache 서버 실행 후 브라우저 창에 'http://localhost/photoweb.php' 입력 후 기능 사용 후 `db_config.py` 컴파일 및 view_joined_data.py 실행해서 결과 조회

---
