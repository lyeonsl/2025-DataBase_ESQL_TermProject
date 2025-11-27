import pymysql
from db_config import get_connection
from view_joined_data import view_joined_data

# 데이터 삭제 (DELETE)
def delete_photo_by_id(photo_id):
    # 특정 PHOTO_ID를 가진 사진 및 관련 상세정보를 삭제
    conn = get_connection()
    if not conn: return
    
    try:
        with conn.cursor() as cursor:
            # (1) DETAILS 테이블 데이터 삭제 (FOREIGN KEY 때문에 PHOTOS보다 먼저 삭제해야 함)
            # 가정: DETAILS 테이블의 PHOTO_ID는 ON DELETE CASCADE 설정이 안 되어 있을 수 있으므로 수동 삭제
            sql_details = "DELETE FROM DETAILS WHERE PHOTO_ID = %s"
            cursor.execute(sql_details, (photo_id,))
            print(f"  > DETAILS 테이블에서 PHOTO_ID={photo_id} 관련 데이터 {cursor.rowcount}개 삭제 완료.")
            
            # (2) PHOTOS 테이블 데이터 삭제
            sql_photos = "DELETE FROM PHOTOS WHERE PHOTO_ID = %s"
            affected_rows = cursor.execute(sql_photos, (photo_id,))

            if affected_rows > 0:
                conn.commit()
                print(f"DELETE 성공: PHOTO_ID={photo_id}의 사진이 PHOTOS 테이블에서 삭제되었습니다.")
            else:
                conn.rollback()
                print(f"DELETE 실패: PHOTO_ID={photo_id}를 찾을 수 없습니다.")

    except pymysql.Error as e:
        print(f"DELETE 실행 오류 (SQLCODE: {e.args[0]}): {e.args[1]}")
        conn.rollback()
    finally:
        conn.close()

if __name__ == "__main__":
    print("\n\n=============== 데이터 삭제 (DELETE) ===============")
    # PHOTO_ID 10번 사진을 삭제 (DETAILS 테이블의 관련 데이터도 함께 삭제됨)
    delete_photo_by_id(10)
    view_joined_data() # 삭제 후 결과 확인
