# yc152_php

#系统统计

    */1 * * * *     /bin/sh      /var/www/html/yc152_php/bin/start.sh             >/dev/null&
    */1 * * * *     /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab doPaid >/dev/null&
    */2 * * * *     /usr/bin/php /var/www/html/yc152_php/bin/shell.php media cdn  >/dev/null&
    */15 * * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab reportServer >/dev/null&
    */15 * * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab reportServerHour>/dev/null&
    */30 * * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab reportDomain>/dev/null&
    */10 * * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab reportAgentV3>/dev/null&
    */15 * * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab reportUserChannel>/dev/null&


#ES刷新

    #10 3 * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab asyncMovie>/dev/null&
    #10 4 * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab asyncComics>/dev/null&
    #10 5 * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab asyncNovel>/dev/null&
    #10 6 * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab asyncAuido>/dev/null&
    #10 7 * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab asyncPost>/dev/null&
    #10 8 * * *    /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab asyncUser>/dev/null&

#业务统计

    #0 0 */5 * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab sitemap >/dev/null&
    0 0 */5 * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab esAnalyzer >/dev/null&
    #0 3 * * *  /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab statsMovie >/dev/null&
    #10 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab statsComics >/dev/null&
    #20 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab statsNovel >/dev/null&
    #30 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab statsAuido >/dev/null&
    #40 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab statsPost >/dev/null&
    #50 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab statsAi >/dev/null&

#业务资源更新

    #10 5 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php media resource movie >/dev/null&
    #10 6 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php media resource comcis >/dev/null&
    #10 7 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php media resource novel >/dev/null&
    #10 8 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php media resource audio >/dev/null&

#业务数据清理

    10 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean user >/dev/null&
    30 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean app_log >/dev/null&
    40 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean report_log >/dev/null&
    50 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean user_order >/dev/null&
    50 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean user_recharge >/dev/null&
    50 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean collections >/dev/null&

    30 2 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean movie_history >/dev/null&
    30 3 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean comics_history >/dev/null&
    30 4 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean audio_history >/dev/null&
    30 5 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean novel_history >/dev/null&
    30 6 * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php crontab clean post_history >/dev/null&

#中心对接

    # */10 * * * * /usr/bin/php /var/www/html/yc152_php/bin/shell.php center adv sync >/dev/null&
