import pymysql
from db_config import get_connection
from view_joined_data import view_joined_data

def insert_sample_data():
    # USERS -> ALBUMS -> PHOTOS -> DETAILS 순서로 데이터를 삽입
    conn = get_connection()
    if not conn: return
    
    try:
        with conn.cursor() as cursor:
            # (1) 사용자 10명 추가
            users = [('김숙명', 'sook@sm.ac.kr'), ('김눈송', 'noonsong@sm.ac.kr'), ('최미래', 'mr@sm.ac.kr'),
                    ('박숙명', 'park@sm.ac.kr'), ('이지민', 'jimin@sm.ac.kr'), ('최강', 'chgang@sm.ac.kr'),
                    ('김연서', 'yeons@sm.ac.kr'), ('도경수', 'dks@sm.ac.kr'), ('김종인', 'jongin@sm.ac.kr'),
                    ('변의주', 'EJ@sm.ac.kr')]
            for name, email in users:
                cursor.execute("INSERT INTO USERS (USERNAME, EMAIL) VALUES (%s, %s)", (name, email))
            user1_id = 1 # 첫 번째 사용자의 ID를 사용 (AUTO_INCREMENT에 의해)
            print("[1] 사용자 10명 생성 완료")

            # (2) 앨범 12개 추가 (사용자1(초기값)에게 2개, 사용자2(김숙명)에게 2개 할당, 나머지 사용자에게 1개씩 할당)
            albums = [
                # User 1 (ID: 1) - 2개 할당
                ('제주도_여행_일지', 1), ('나만의_레시피_북', 1), 
                # User 2 (ID: 2) - 2개 할당
                ('스터디_노트_모음', 2), ('강아지_성장_기록', 2), 
                # User 3 to 10 (ID: 3~10) - 1개씩 할당 (총 8개)
                ('운동_기록_챌린지', 3), ('인생_영화_리뷰', 4),
                ('취미_가드닝_사진', 5), ('테크_기기_리뷰', 6),
                ('연휴_가족_여행', 7), ('미술_스케치_모음', 8),
                ('음악_콘서트_후기', 9), ('건축_탐방_사진', 10)
            ]
            for title, uid in albums:
                cursor.execute("INSERT INTO ALBUMS (ALBUMTITLE, USER_ID) VALUES (%s, %s)", (title, uid))
            
            album_ids = list(range(1, 13)) # 앨범 ID 1번부터 12번까지
            print("[2] 앨범 12개 생성 완료")

            # (3) 사진 15개 추가
            photos = []
            total_photos = 15
            # 사진의 컨셉을 다양하게 정의하여 파일명과 캡션에 사용
            photo_concepts = ["풍경", "인물", "음식", "반려동물", "도서", "스튜디오", "여행", "일상"]
            
            for i in range(1, total_photos + 1):
                # 12개의 앨범에 15개 사진을 순환 할당
                assigned_album_id = album_ids[(i - 1) % len(album_ids)]
                concept = photo_concepts[(i - 1) % len(photo_concepts)]
                
                filename = f'Photo_{i:02d}_{concept}.jpg'
                caption = f'Album {assigned_album_id} - {concept} 기록'
                photos.append((filename, caption, assigned_album_id))

            for filename, caption, aid in photos:
                cursor.execute("INSERT INTO PHOTOS (FILENAME, CAPTION, ALBUM_ID) VALUES (%s, %s, %s)", (filename, caption, aid))
            
            photo_ids = list(range(1, total_photos + 1)) # 사진 ID 1번부터 15번까지
            print(f"[3] 사진 {total_photos}개 업로드 완료")

            # (4) 상세정보 15개 추가
            details = []
            camera_models = ['iPhone 15 Pro', 'Canon R5', 'Sony A7 IV', 'Samsung S23', 'DJI Mini 3', 'GoPro 12']
            resolutions = ['4032x3024', '8192x5464', '6000x4000', '3000x2000', '3840x2160', '5312x2988']
            
            for i in range(total_photos): # 15개의 상세정보를 15개의 사진 ID에 1:1 매칭
                pid = photo_ids[i]
                model = camera_models[i % len(camera_models)]
                res = resolutions[i % len(resolutions)]
                size_kb = 3000 + i * 200 # 파일 크기 (KB)
                
                details.append((model, res, size_kb, pid))
            
            for model, res, size, pid in details:
                cursor.execute("INSERT INTO DETAILS (CAMERA_MODEL, RESOLUTION, SIZE, PHOTO_ID) VALUES (%s, %s, %s, %s)", (model, res, size, pid))
            
            print(f"[4] 상세정보 {total_photos}개 저장 완료")

        conn.commit() # 실제 저장
        print("\n=== 모든 샘플 데이터가 정상적으로 저장되었습니다 ===")

    except pymysql.Error as e:
        print(f"SQL 실행 오류 (SQLCODE: {e.args[0]}): {e.args[1]}")
        conn.rollback() # 에러나면 취소
    finally:
        conn.close()

if __name__ == "__main__":
    print("\n\n=============== 데이터 삽입 (INSERT) ===============")
    insert_sample_data() # 데이터 넣기
    view_joined_data() # 삽입 후 데이터 확인