<style>
    body {
        /* background: url(admin/assets/img/main-bg.jpg);
        background-repeat: no-repeat;
        background-size: cover; */
        background: #fff;
    }

    main {
        height: 90vh;
    }

    h3 {
        font-size: 1.5rem
    }

    .full-container {
        width: 100%;
        height: 100%;
        display: flex;
    }

    .left-side {
        background: url(admin/assets/img/green-engraving-r.png);
        background-repeat: no-repeat;
        background-size: cover;
        width: calc(50%);
        height: calc(100%);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .left-side img {
        width: 100%;
    }

    .singleCard {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        height: 100px;
        justify-content: space-between;
        margin-bottom: 5px;
        color: #fff;

        border-radius: 0 10px 10px 0;

    }

    .singleCard p {
        font-size: 20px;
        font-weight: 900;
        color: #fff;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);

    }

    .singleCard h1 {
        font-size: 4.5rem;
        font-weight: 900;
        color: #fff;
        margin-left: 25px;
        border-right: 1px solid white;
        padding-right: 20px;
    }

    .singleCard p span {
        margin: 0 10px;
    }

    .singleCard p #window {
        font-size: 30px;
    }

    .singleCard p #squeue {
        font-size: 40px;
    }

    .right-side {
        background: url(admin/assets/img/green-engraving-l.png);
        background-repeat: no-repeat;
        background-size: cover;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: calc(50%);
        height: calc(100%);
        text-align: start;
        padding-right: 25px;
    }

    .slideShow {
        display: flex;
        justify-content: center;
        align-items: center;
        width: calc(100%);
        /* height: calc(100%); */
        padding: auto;
    }

    .slideShow img,
    .slideShow video {
        max-width: calc(100%);
        max-height: calc(100%);
        opacity: 0;
        transition: all .5s ease-in-out;
        border-radius: 20px;

    }

    .slideShow video {
        width: calc(100%);
    }



    .cards-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 5px;
    }

    .container-fluid {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .main-card {
        position: relative;
        height: 50px;
        direction: rtl;
        display: -ms-flexbox;
        display: flex;
        justify-content: space-around;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, .125);
        border-radius: 10px;
        margin: 5px;
        text-align: right;
    }

    .main-card .card-body {
        padding: 0.25rem;
    }

    .main-card h3 {
        display: inline-block;
    }

    .main-card .main-head {
        width: 100%;
        text-align: center;
        border: 1px solid black;
        margin: 0 5px;
        border-radius: 10px;
        background-color: #85FFBD;
    }

    .card {
        height: 75px;
        box-shadow: 0px 10px 10px rosybrown;
        margin: 5px;
        border-radius: 50px;
        direction: rtl;
    }

    a.btn.btn-sm.btn-success {
        z-index: 99999;
        background: #55608f;
        border: none;
    }

    .company {
        display: flex;
        gap: 10px;
        align-items: center;
        z-index: 99999;
        margin-bottom: 5px;
        justify-content: space-between;
    }

    #company_image {
        width: 120px;
        height: 120px;
    }

    #company_title {
        font-weight: 900;
        color: #1592d1;
        font-size: 40px;
        text-shadow: -3px 3px #28a74575;
        text-align: center;
    }
</style>
<?php include "admin/db_connect.php" ?>
<?php
$trans = $conn->query("SELECT * FROM transaction_windows where status = 1 order by name asc");

?>

<div class="full-container">
    <!--<audio controls autoplay style="display:none"><source src="tts/audios/test.mp3"/></audio>-->
    <div class="left-side">
        <div class="container pl-0">
            <div>
                <h3 class="text-center"><b>يتم الأن خدمة صاحب التذكرة رقم</b></h3>
            </div>

            <?php
            $colors_arr = ['#2E78DB', '#6AB2ED', '#9CD9F5', '#6AB2ED', '#2E78DB'];
            $i = 1;
            $j = 0;
            while ($row = $trans->fetch_assoc()) :
            ?>

                <div class="singleCard" data-tid="<?= $row['transaction_id'] ?>" data-wid="<?= $row['id'] ?>" dir="rtl" style="
                background-color: <?php
                                    if (isset($colors_arr[$j])) {
                                        echo $colors_arr[$j];
                                        $j++;
                                    } else {
                                        $j = 0;
                                        echo $colors_arr[$j];
                                        $j++;
                                    }
                                    ?>">
                    <p><span id="window"></span>&lAarr;<span id="squeue"></span></p>
                    <h1><?= $i++ ?></h1>
                </div>
            <?php endwhile; ?>

        </div>
    </div>

    <div class="right-side">
        <div class="company">
            <img src="<?php echo isset($_SESSION['setting_image']) ? 'admin/assets/img/' . $_SESSION['setting_image'] : 'admin/assets/img/logo.jpg' ?>" alt="" id="company_image">
            <p id="company_title"><?php echo isset($_SESSION['setting_name']) ?  $_SESSION['setting_name'] : 'Transaction Queuing System' ?></p>
        </div>
        <?php
        $uploads = $conn->query("SELECT * FROM file_uploads order by rand() ");
        $slides = array();
        while ($row = $uploads->fetch_assoc()) {
            $slides[] = $row['file_path'];
        }
        ?>
        <div class="slideShow">

        </div>
    </div>
</div>
<div id="x-container" style="display: none;">
    <?php
    // $trans = $conn->query("SELECT * FROM transactions where status = 1 order by name asc");
    $trans = $conn->query("SELECT * FROM transaction_windows where status = 1 order by name asc");

    while ($row = $trans->fetch_assoc()) :
    ?>
        <option value=""></option>
        <button type="button" class="btn transaction-x" data-tid="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></button>
    <?php endwhile; ?>
</div>


<script type="text/javascript">
    var slides = <?php echo json_encode($slides) ?>;
    var scount = slides.length;
    if (scount > 0) {
        $(document).ready(function() {
            render_slides(0)
        })
    }

    function render_slides(k) {
        if (k >= scount)
            k = 0;
        var src = slides[k]
        k++;
        var t = src.split('.');
        var file;
        t = t[1];
        if (t == 'webm' || t == "mp4") {
            file = $("<video id='slide' src='admin/assets/uploads/" + src + "' onended='render_slides(" + k + ")' autoplay='true' muted='muted'></video>");
        } else {
            file = $("<img id='slide' src='admin/assets/uploads/" + src + "' onload='slideInterval(" + k + ")' />");
        }
        //console.log(file)
        if ($('#slide').length > 0) {
            $('#slide').css({
                "opacity": 0
            });
            setTimeout(function() {
                $('.slideShow').html('');
                $('.slideShow').append(file)
                $('#slide').css({
                    "opacity": 1
                });
                if (t == 'webm' || t == "mp4")
                    $('video').trigger('play');


            }, 500)
        } else {
            $('.slideShow').append(file)
            $('#slide').css({
                "opacity": 1
            });

        }

    }

    function slideInterval(i = 0) {
        setTimeout(function() {
            render_slides(i)
        }, 5000)

    }
</script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.singleCard').each(function() {
            var card = $(this);
            var tid = card.data('tid');
            var wid = card.data('wid');

            var previousResponse;


            var renderServe = setInterval(function() {
                $.ajax({
                    url: 'admin/ajax.php?action=get_queue',
                    method: "POST",
                    data: {
                        id: tid,
                        wid: wid
                    },
                    success: function(resp) {
                        try {
                            parsedResp = JSON.parse(resp);
                        } catch (error) {
                            // Handle non-JSON response here
                            return;
                        }
                        resp = JSON.parse(resp);
                        if (resp.status == 1) {
                            card.find('#squeue').html(resp.data.tsymbol + resp.data.queue_no);
                            card.find('#window').html(resp.data.wname);

                            previousResponse = resp;
                        }
                    }
                });
            }, 2000);
        });
        // var renderTranss = setInterval(function() {
        //     location.reload()
        // }, 60000);
        //get trans sound
        $(document).ready(function() {

            $('.transaction-x').each(function() {
                var card = $(this);
                var tid = card.data('tid');

                var previousResponse = {
                    status: '',
                    data: {
                        queue_no: '',
                        date_created: '',
                        recall: ''
                    }
                };

                var renderServe = setInterval(function() {
                    $.ajax({
                        url: 'admin/ajax.php?action=get_queue_sound',
                        method: "POST",
                        data: {
                            id: tid
                        },
                        success: function(resp) {
                            try {
                                parsedResp = JSON.parse(resp);
                            } catch (error) {
                                return;
                            }
                            resp = JSON.parse(resp);
                            if (resp.status == 1) {
                                if (
                                    (resp.data.queue_no !== previousResponse.data.queue_no &&
                                        resp.data.date_created !== previousResponse.data.date_created) || resp.data.recall !== previousResponse.data.recall
                                ) {
                                    let start = 'البطاقة رقم ';
                                    let symbol = resp.data.tsymbol;
                                    let num = resp.data.queue_no;
                                    let to = ' إلى ';
                                    let wnum = resp.data.wname;
                                    let str = start + symbol + ' ' + num + to + wnum;
                                    fetch('tts/tts.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded'
                                            },
                                            body: 'text=' + encodeURIComponent(str)
                                        })

                                        .catch(error => {
                                            console.error('AJAX request failed:', error);
                                        });

                                }

                                previousResponse = resp;
                            }
                        }
                    });
                }, 2000);
            });

        });
    });
</script>