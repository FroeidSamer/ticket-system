<style>
    .left-side {
        width: 100%;
        height: calc(100%);
        background: #ffffffc7;
        display: flex;
        justify-content: center;
        /* align-items: center; */
        overflow-y: scroll;
    }

    .cards-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px;
    }

    a.btn.btn-sm.btn-success {
        z-index: 99999;
        position: fixed;
        left: 1rem;
    }

    .speaker {
        z-index: 99999;
        position: fixed;
        left: 1rem;
        top: 4rem;
        color: #fff;
    }
</style>
<?php
$trans = $conn->query("SELECT * FROM transactions where status = 1 order by name asc");
?>
<a href="index.php" class="btn btn-sm btn-success"><i class="fa fa-home"></i> Home</a>
<div class="left-side">
    <div class="col-md-10 offset-md-1 cards-container">
        <?php
        while ($row = $trans->fetch_assoc()) :
        ?>
            <div class="card" data-tid="<?php echo $row['id'] ?>">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body bg-primary">
                                <h2 class="text-center text-white"><b><?php echo $row['name'] ?></b></h2>
                            </div>
                        </div>
                        <br>
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h3 class="text-center"><b>Now Waiting</b></h3>
                            </div>
                            <div class="card-body card-details">
                                <h2 class="text-center counter"></h2>
                                <hr class="divider">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>



<script>
    $(document).ready(function() {
        $('.card').each(function() {
            var card = $(this);
            var tid = card.data('tid');

            var previousResponse;


            var renderServe = setInterval(function() {
                $.ajax({
                    url: 'admin/ajax.php?action=get_waiting_queue',
                    method: "POST",
                    data: {
                        id: tid
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
                            let symbol = resp.symbol
                            let len = Object.keys(resp.data).length;

                            if (JSON.stringify(resp) !== JSON.stringify(previousResponse)) {
                                $.each(resp.data, function(i, item) {
                                    card.find('.counter').html(len);
                                });
                            }

                            previousResponse = resp;
                        }
                    }
                });
            }, 5000);
        });
    });
</script>