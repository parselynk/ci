<h2><?php echo $title; ?></h2>

<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>

<?php foreach ($news as $news_item): ?>

        <h3><?php echo $news_item->title; ?></h3>
        <div class="main">
                <?php echo $news_item->show_text(); ?>
            <br><br>
                <?php echo $news_item->slug; ?>
            
        </div>
        <p><a href="<?php echo site_url('news/'.$news_item->id); ?>">View article</a></p>
            <hr>

<?php endforeach; ?>

            

