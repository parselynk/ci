<?php
echo '<h2>'.$title.'</h2>';
echo $news_item->title.'</br>';
echo $news_item->slug.'</br>';
echo '<br>From show_them()<hr>';
echo $news_item->show_them();
echo '<br><br>checking show_date()';
echo $news_item->show_date();
