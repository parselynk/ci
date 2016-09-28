
    <!-- Body -->
        <h1><?php echo $title; ?></h1>
        <ul>
            <?php foreach($news as $new) { ?>
            <li><h3><?php echo $new->title ?></h3></li>
                <p><?php echo $new->show_text() ?></p>
            <?php } ?>
        </ul>
    <!-- Body ends -->
