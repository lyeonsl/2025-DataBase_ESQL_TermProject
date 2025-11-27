<?php
// photoweb.php에서 폼 요청을 받아 DB 작업을 수행하고 결과를 photoweb.php로 다시 리다이렉트

// 1. DB 연결 설정 (photoweb.php와 동일)
$host = 'localhost';
$user = 'root';
$password = ' ';    // <--- yours
$database = 'photosystem';
$port = 3306; 

$conn = mysqli_connect('localhost', $user, $password, $database, $port);
if (!$conn) {
    die("MySQL 연결 실패: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

// 입력값 검증
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['action'])) {
    header("Location: photoweb.php?status=" . urlencode("잘못된 접근입니다."));
    exit();
}

$action = $_POST['action'];
$message = "알 수 없는 작업";


// 2. INSERT (사용자, 앨범, 사진, 상세정보 동시 삽입)
if ($action == 'insert') {
    $username = $_POST['username'];
    $albumtitle = $_POST['albumtitle'];
    $filename = $_POST['filename'];
    $camera_model = $_POST['camera_model'];
    $resolution = '1920x1080'; // 기본 해상도 설정
    $size = 5000; // 기본 크기 설정

    mysqli_begin_transaction($conn); // 트랜잭션 시작

    try {
        // 1) USERS 테이블 삽입
        $stmt = mysqli_prepare($conn, "INSERT INTO USERS (USERNAME) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $user_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // 2) ALBUMS 테이블 삽입
        $stmt = mysqli_prepare($conn, "INSERT INTO ALBUMS (ALBUMTITLE, USER_ID) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "si", $albumtitle, $user_id);
        mysqli_stmt_execute($stmt);
        $album_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // 3) PHOTOS 테이블 삽입
        $stmt = mysqli_prepare($conn, "INSERT INTO PHOTOS (FILENAME, ALBUM_ID) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "si", $filename, $album_id);
        mysqli_stmt_execute($stmt);
        $photo_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // 4) DETAILS 테이블 삽입
        $stmt = mysqli_prepare($conn, "INSERT INTO DETAILS (CAMERA_MODEL, RESOLUTION, SIZE, PHOTO_ID) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssii", $camera_model, $resolution, $size, $photo_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn); // 모든 쿼리 성공 시 커밋
        $message = "INSERT 성공! 4개 테이블에 데이터가 추가되었습니다. (User: $username)";

    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($conn); // 오류 발생 시 롤백
        $message = "INSERT 실패! DB 오류: " . $exception->getMessage();
    }
} 

// 3. UPDATE (사진 상세정보 - 해상도 수정)
elseif ($action == 'update') {
    $photo_id = (int)$_POST['photo_id'];
    $new_resolution = $_POST['new_resolution'];

    $sql = "UPDATE DETAILS SET RESOLUTION = ? WHERE PHOTO_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $new_resolution, $photo_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $message = "UPDATE 성공! PHOTO_ID {$photo_id}의 해상도가 {$new_resolution}로 수정되었습니다.";
        } else {
            $message = "UPDATE 실패/변화 없음. PHOTO_ID {$photo_id}를 찾을 수 없거나 데이터가 변경되지 않았습니다.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "UPDATE 실패! 쿼리 준비 오류: " . mysqli_error($conn);
    }
}

// 4. DELETE (앨범 삭제)
elseif ($action == 'delete') {
    $album_id = (int)$_POST['album_id'];

    // 앨범 삭제: FOREIGN KEY (ON DELETE CASCADE) 설정이 되어있다고 가정하고 ALBUMS만 삭제
    // 설정이 안 되어 있다면 PHOTOS -> DETAILS 순서로 수동 삭제해야 함
    $sql = "DELETE FROM ALBUMS WHERE ALBUM_ID = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $album_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $message = "DELETE 성공! ALBUM_ID {$album_id}와 관련 사진(들)이 모두 삭제되었습니다.";
        } else {
            $message = "DELETE 실패. ALBUM_ID {$album_id}를 찾을 수 없습니다.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "DELETE 실패! 쿼리 준비 오류: " . mysqli_error($conn);
    }
}

// 5. 작업 완료 후 photoweb.php로 돌아가 메시지 출력
mysqli_close($conn);
header("Location: photoweb.php?status=" . urlencode($message));
exit();
?>