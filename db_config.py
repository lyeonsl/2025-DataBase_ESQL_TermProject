import pymysql

# DB 연결 설정 (필수)
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': ' ', # <-- yours
    'db': 'PHOTOSYSTEM',
    'charset': 'utf8',
}

# DB 연결 객체 생성 및 반환 함수
def get_connection():
    try:
        conn = pymysql.connect(**db_config)
        return conn
    except pymysql.Error as e:
        print(f"DB 연결 실패 (SQLCODE: {e.args[0]}): {e.args[1]}")
        return None