<?php

return [
    'feeds' => [
        'theme' => [
            /*
             * Here you can specify which class and method will return
             * the items that should appear in the feed. For example:
             * 'App\Model@getAllFeedItems'
             *
             * You can also pass an argument to that method:
             * ['App\Model@getAllFeedItems', 'argument']
             */
            'items' => 'App\Models\Post@getFeedItems',
            /*
             * The feed will be available on this url.
             */
            'url' => '/feed/product',
            'title' => 'فروشگاه اینترنتی',
            'description' => 'فروش انواع محصولات به صورت آنلاین',
            'language' => 'fa-IR',
            /*
             * The view that will render the feed.
             */
            'view' => 'feed::rss',
            /*
             * The type to be used in the <link> tag
             */
            'type' => 'application/atom+xml',
        ],
    ],
];
