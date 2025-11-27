import pymysql
from db_config import get_connection
from view_joined_data import view_joined_data

# 데이터 업데이트 (UPDATE)
def update_photo_caption(photo_id, new_caption):
    # 특정 PHOTO_ID를 가진 사진의 캡션(CAPTION)을 수정
    conn = get_connection()
    if not conn: return
    
    sql = "UPDATE PHOTOS SET CAPTION = %s WHERE PHOTO_ID = %s"

    try:
        with conn.cursor() as cursor:
            # 쿼리 실행 (new_caption, photo_id)
            affected_rows = cursor.execute(sql, (new_caption, photo_id))
            
            if affected_rows > 0:
                conn.commit()
                print(f"\nUPDATE 성공: PHOTO_ID={photo_id}의 캡션이 '{new_caption}'로 수정되었습니다.")
            else:
                conn.rollback()
                print(f"\n UPDATE 실패: PHOTO_ID={photo_id}를 찾을 수 없거나 캡션이 변경되지 않았습니다.")

    except pymysql.Error as e:
        print(f"UPDATE 실행 오류 (SQLCODE: {e.args[0]}): {e.args[1]}")
        conn.rollback()
    finally:
        conn.close()

if __name__ == "__main__":
    print("\n\n=============== 데이터 수정 (UPDATE) ===============")
    # PHOTO_ID 5번 사진의 캡션을 수정
    update_photo_caption(5, "수정된 캡션: 갤러리 전시회 관람 후기")
    view_joined_data()