<?php
// photoweb.php

// 1. DB 연결 설정 (필수)
$host = 'localhost';
$user = 'root';
$password = ' ';    // <--- yours
$database = 'photosystem';
$port = 3306;

// IP 주소를 명시하여 연결 문제 재발 방지
$conn = mysqli_connect('localhost', $user, $password, $database, $port);
if (!$conn) {
    die("MySQL 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

// 사용자가 요청한 화면 뷰를 결정 (기본값: 전체 Join 결과)
// 기본 뷰는 full_join으로 유지, CRUD 관련 뷰는 insert, update, delete로 나눔
$view = isset($_GET['view']) ? $_GET['view'] : 'full_join';
$selected_album_id = isset($_GET['album_id']) ? intval($_GET['album_id']) : null;
$status_message = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>ESQL 프로젝트 - 사진 관리 시스템 통합</title>
    <style>
        body { font-family: 'Malgun Gothic', 'Arial', sans-serif; margin: 30px; background-color: #f0f2f5; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; background-color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { color: #3b5998; border-bottom: 3px solid #3b5998; padding-bottom: 10px; margin-bottom: 25px; }
        h2 { color: #555; margin-top: 30px; border-left: 5px solid #4CAF50; padding-left: 10px; }
        /* IUD 폼 스타일 */
        .crud-section { background-color: #e9ebee; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .crud-section h3 { color: #3b5998; margin-top: 0; }
        .crud-section form { display: flex; flex-direction: column; gap: 10px; width: 400px; margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .crud-section input[type="text"], .crud-section input[type="number"] { padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; }
        .crud-section input[type="submit"] { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; margin-top: 5px; }
        .insert-btn { background-color: #4CAF50; }
        .update-btn { background-color: #2196F3; }
        .delete-btn { background-color: #f44336; }
        /* Status 메시지 */
        .status-message { padding: 10px; margin-bottom: 15px; border-radius: 5px; font-weight: bold; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        /* 네비게이션 버튼 스타일 */
        .nav-buttons { margin-bottom: 20px; }
        .nav-buttons a {
            padding: 10px 15px; 
            background-color: #4CAF50; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            margin-right: 10px;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .nav-buttons a:hover { background-color: #45a049; }
        .nav-buttons .active { background-color: #3b5998; }
        /* CRUD 버튼 색상 */
        .nav-buttons .crud-btn { background-color: #FF9800; }
        .nav-buttons .crud-btn:hover { background-color: #F57C00; }

        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background-color: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #e9ebee; color: #3b5998; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .summary { margin-bottom: 15px; font-weight: bold; color: #555; }
        .album-item { padding: 8px 0; border-bottom: 1px dotted #ccc; }
        .album-item a { color: #2196F3; text-decoration: none; font-weight: bold; }
        .album-item span { color: #666; font-size: 0.9em; margin-left: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h1>통합 사진 관리 시스템</h1>

    <?php if ($status_message): ?>
    <div class="status-message"><?php echo $status_message; ?></div>
    <?php endif; ?>

    <div class="nav-buttons">
        <a href="?view=full_join" class="<?php echo ($view == 'full_join' ? 'active' : ''); ?>">4-Table Join 전체 결과</a>
        <a href="?view=album_list" class="<?php echo ($view == 'album_list' ? 'active' : ''); ?>">앨범별 상세 사진 조회</a>
        <a href="?view=insert" class="<?php echo ($view == 'insert' ? 'active crud-btn' : 'crud-btn'); ?>">데이터 삽입 (INSERT)</a>
        <a href="?view=update" class="<?php echo ($view == 'update' ? 'active crud-btn' : 'crud-btn'); ?>">데이터 수정 (UPDATE)</a>
        <a href="?view=delete" class="<?php echo ($view == 'delete' ? 'active crud-btn' : 'crud-btn'); ?>">데이터 삭제 (DELETE)</a>
    </div>

    <?php 
    // 2. 화면 분기 처리

    // 2.1. 데이터 삽입 (INSERT) 뷰
    if ($view == 'insert') { ?>
        <div class="crud-section">
            <h2>데이터 삽입 (INSERT) 기능</h2>
            <p>새로운 사용자, 앨범, 사진, 상세정보를 4개 테이블에 동시 삽입합니다.</p>

            <h3>1. INSERT: 사용자, 앨범, 사진, 상세정보 삽입</h3>
            <form action="process_crud.php" method="POST">
                <input type="hidden" name="action" value="insert">
                <input type="text" name="username" placeholder="사용자 이름 (예: NewUser1)" required>
                <input type="text" name="albumtitle" placeholder="앨범 제목 (예: Summer Trip)" required>
                <input type="text" name="filename" placeholder="파일명 (예: beach_001.jpg)" required>
                <input type="text" name="camera_model" placeholder="카메라 기종 (예: Canon EOS R5)" required>
                <input type="submit" value="-- 4개 테이블 동시 INSERT 실행" class="insert-btn">
            </form>
        </div>
    <?php
    }

    // 2.2. 데이터 수정 (UPDATE)
    elseif ($view == 'update') { ?>
        <div class="crud-section">
            <h2>데이터 수정 (UPDATE) 기능</h2>
            <p>기존 사진의 **PHOTO_ID**를 지정하여 해상도 정보를 수정합니다.</p>

            <h3>2. UPDATE: 사진 해상도 수정 (PHOTO_ID 필요)</h3>
            <form action="process_crud.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="number" name="photo_id" placeholder="수정할 PHOTO_ID 입력 (예: 1)" required>
                <input type="text" name="new_resolution" placeholder="새로운 해상도 (예: 9999x9999)" required>
                <input type="submit" value="-- 해상도 UPDATE 실행" class="update-btn">
            </form>
        </div>
    <?php
    }

    // 2.3. 데이터 삭제 (DELETE)
    elseif ($view == 'delete') { ?>
        <div class="crud-section">
            <h2>데이터 삭제 (DELETE) 기능</h2>
            <p>앨범 ID를 지정하여 앨범을 삭제합니다. **FOREIGN KEY CASCADE**가 적용되어 관련 사진 및 상세 정보가 자동으로 삭제됩니다.</p>

            <h3>3. DELETE: 앨범 삭제 (앨범 ID 필요)</h3>
            <p>**주의:** 앨범 삭제 시, **PHOTOS 및 DETAILS 테이블의 관련 사진들도 모두 삭제**됩니다.</p>
            <form action="process_crud.php" method="POST" onsubmit="return confirm('정말로 이 앨범을 삭제하시겠습니까? 관련 사진도 모두 삭제됩니다.');">
                <input type="hidden" name="action" value="delete">
                <input type="number" name="album_id" placeholder="삭제할 ALBUM_ID 입력 (예: 1)" required>
                <input type="submit" value="-- 앨범 DELETE 실행" class="delete-btn">
            </form>
        </div>
    <?php
    }

    // 2.4. 4-Table Join 결과 출력
    elseif ($view == 'full_join') {
        $sql = "SELECT U.USERNAME, A.ALBUMTITLE, P.FILENAME, D.CAMERA_MODEL, D.RESOLUTION
                FROM USERS U
                INNER JOIN ALBUMS A ON U.USER_ID = A.USER_ID
                INNER JOIN PHOTOS P ON A.ALBUM_ID = P.ALBUM_ID
                INNER JOIN DETAILS D ON P.PHOTO_ID = D.PHOTO_ID
                ORDER BY U.USERNAME, A.ALBUMTITLE, P.FILENAME
                LIMIT 50";

        $result = mysqli_query($conn, $sql);
        if (!$result) {
            die("쿼리 실행 오류: " . mysqli_error($conn));
        }
        $num_rows = mysqli_num_rows($result);
        ?>
        
        <h2>4개 테이블 Join 결과 (핵심 입증)</h2>
        <div class="summary">
            데이터베이스에서 조회된 총 튜플 수: <?php echo $num_rows; ?>개 (Python과 상호 검증 포인트)
        </div>

        <table>
            <thead>
                <tr>
                    <th>사용자</th>
                    <th>앨범제목</th>
                    <th>파일명</th>
                    <th>카메라기종</th>
                    <th>해상도</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['USERNAME']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ALBUMTITLE']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['FILENAME']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['CAMERA_MODEL']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['RESOLUTION']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    <?php 
    } 

    // 2.5. 앨범별 상세 사진 조회
    elseif ($view == 'album_list') { 
        ?>
        <h2>앨범별 상세 사진 조회 (응용 기능)</h2>

        <?php
        // 앨범 목록 가져오는 쿼리
        $sql_albums = "SELECT A.ALBUM_ID, A.ALBUMTITLE, U.USERNAME 
                        FROM ALBUMS A 
                        INNER JOIN USERS U ON A.USER_ID = U.USER_ID
                        ORDER BY U.USERNAME, A.ALBUMTITLE";
        $result_albums = mysqli_query($conn, $sql_albums);

        if (!$result_albums) {
            die("앨범 목록 쿼리 실행 오류: " . mysqli_error($conn));
        }
        ?>

        <div class="album-list">
            <h3>전체 앨범 목록 (<?php echo mysqli_num_rows($result_albums); ?>개)</h3>
            <?php while ($album = mysqli_fetch_assoc($result_albums)): ?>
                <div class="album-item">
                    <a href="?view=album_list&album_id=<?php echo $album['ALBUM_ID']; ?>">
                        <?php echo htmlspecialchars($album['ALBUMTITLE']); ?>
                    </a>
                    <span>(소유자: <?php echo htmlspecialchars($album['USERNAME']); ?>)</span>
                </div>
            <?php endwhile; ?>
        </div>
        
        <?php
        // 2.5.1. 선택된 앨범의 사진 상세 정보 출력
        if ($selected_album_id) {
            $sql_photos = "SELECT P.FILENAME, P.CAPTION, D.CAMERA_MODEL, D.RESOLUTION, P.PHOTO_ID
                            FROM PHOTOS P
                            INNER JOIN DETAILS D ON P.PHOTO_ID = D.PHOTO_ID
                            WHERE P.ALBUM_ID = ?";
            
            $stmt = mysqli_prepare($conn, $sql_photos);
            mysqli_stmt_bind_param($stmt, "i", $selected_album_id);
            mysqli_stmt_execute($stmt);
            $photos_result = mysqli_stmt_get_result($stmt);

            // 앨범 제목 다시 가져옴
            $title_query = mysqli_query($conn, "SELECT ALBUMTITLE FROM ALBUMS WHERE ALBUM_ID = $selected_album_id");
            $selected_album_title = mysqli_fetch_assoc($title_query)['ALBUMTITLE'];
            $num_photos = mysqli_num_rows($photos_result);
            
            ?>
            <div class="photo-list">
                <h2>선택된 앨범: "<?php echo htmlspecialchars($selected_album_title); ?>"의 사진들 (총 <?php echo $num_photos; ?>개)</h2>
                
                <?php if ($num_photos > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>PHOTO_ID</th>
                            <th>파일명</th>
                            <th>캡션</th>
                            <th>카메라 기종</th>
                            <th>해상도</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($photo = mysqli_fetch_assoc($photos_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($photo['PHOTO_ID']); ?></td>
                                <td><?php echo htmlspecialchars($photo['FILENAME']); ?></td>
                                <td><?php echo htmlspecialchars($photo['CAPTION']); ?></td>
                                <td><?php echo htmlspecialchars($photo['CAMERA_MODEL']); ?></td>
                                <td><?php echo htmlspecialchars($photo['RESOLUTION']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p>이 앨범에는 아직 사진이 업로드되지 않았습니다.</p>
                <?php endif; ?>
            </div>
            <?php 
        } 
    } 

    // 3. 연결 닫기
    mysqli_close($conn); 
    ?>
</div>

</body>
</html>