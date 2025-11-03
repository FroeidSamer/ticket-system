<?php
include('db_connect.php');
if (isset($_GET['id'])) {

    $dailyStatsQuery = "SELECT  COUNT(*) as daily_count FROM staff_statistics  WHERE staff_id =" . $_GET['id'] . " AND DATE(processed_at) = CURDATE()";
    $statsQuery = "SELECT 
                  COUNT(CASE WHEN DATE(processed_at) = CURDATE() THEN 1 END) AS daily_count,
                  COUNT(CASE WHEN MONTH(processed_at) = MONTH(CURDATE()) AND YEAR(processed_at) = YEAR(CURDATE()) THEN 1 END) AS monthly_count,
                  COUNT(CASE WHEN YEAR(processed_at) = YEAR(CURDATE()) THEN 1 END) AS yearly_count
              FROM 
                  staff_statistics

              WHERE 
                  staff_id = ?
                  ";
    $stmt = $conn->prepare($statsQuery);
    $stmt->bind_param("s", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $daily_count = $row['daily_count'];
        $monthly_count = $row['monthly_count'];
        $yearly_count = $row['yearly_count'];
    } else {
        $daily_count = 0;
        $monthly_count = 0;
        $yearly_count = 0;
    }
}
?>
<style>
    .para {
        color: red;
        margin: 10px;
        display: inline;
    }
</style>
<div class="container-fluid" dir="rtl" style="text-align: start;">

    <div id="stats-user">
        <h5 class="text-center my-5">
            إحصائيات المستخدم
        </h5>
        <h6 class="my-5">
            عدد التذاكر المستلمة :
        </h6>
        <p class="para">
            اليوم
        </p>
        <span>
            <?= $daily_count ?>
        </span>
        <p class="para">
            الشهر
        </p>
        <span>
            <?= $monthly_count ?>
        </span>
        <p class="para">
            السنة
        </p>
        <span>
            <?= $yearly_count ?>
        </span>
    </div>

</div>