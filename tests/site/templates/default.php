<?php
    echo $page->title()->value();

    if($page->hasImages()) {
        echo PHP_EOL . $page->testimage()->toFile()->url();
    }
