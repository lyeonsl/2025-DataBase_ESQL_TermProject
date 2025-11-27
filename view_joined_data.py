import pymysql
from db_config import get_connection

# 4개 테이블 조인 조회 (SELECT)
def view_joined_data():
    # USERS, ALBUMS, PHOTOS, DETAILS 4개 테이블을 모두 조인하여 출력하게끔
    conn = get_connection()
    if not conn: return

    # 4개 테이블을 모두 엮는 최대 조인 쿼리
    sql = """
    SELECT U.USERNAME, A.ALBUMTITLE, P.FILENAME, D.CAMERA_MODEL, D.RESOLUTION
    FROM USERS U
    JOIN ALBUMS A ON U.USER_ID = A.USER_ID
    JOIN PHOTOS P ON A.ALBUM_ID = P.ALBUM_ID
    JOIN DETAILS D ON P.PHOTO_ID = D.PHOTO_ID
    ORDER BY U.USERNAME, A.ALBUMTITLE
    """

    try:
        with conn.cursor() as cursor:
            cursor.execute(sql)
            results = cursor.fetchall()
            
            print("\n=======================================================")
            print("=== [Python HLL] 4-Table Join 결과 ===")
            print("=======================================================")
            print(f"{'사용자':<8} | {'앨범제목':<12} | {'파일명':<20} | {'카메라기종':<12} | {'해상도':<10}")
            print("-" * 75)
            for row in results:
                print(f"{row[0]:<8} | {row[1]:<12} | {row[2]:<20} | {row[3]:<12} | {row[4]:<10}")
            print("-------------------------------------------------------")
            print(f"총 {len(results)}개의 튜플 조회 완료.")
                
    except pymysql.Error as e:
        print(f"조회 에러 (SQLCODE: {e.args[0]}): {e.args[1]}")
    finally:
        conn.close()

if __name__ == "__main__":
    view_joined_data()