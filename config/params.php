<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    
    // Добавил адрес хостовой машины, т.к. приложение было запущено в докере
    // IP определяем с помощью docker logs yii2-test-php-1
    // там видно откуда запросы приходят
    'allowedIPs' => ['127.0.0.1', '::1', /*'172.23.0.1'*/]
];
