ALTER TABLE 
    `payments` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD PRIMARY KEY(`id`);

INSERT INTO `settings` (`key`, `value`)
VALUES 
    ('paystack', '{\"is_enabled\":\"0\",\"publishable_key\":\"\",\"secret_key\":\"\",\"webhook_secret\":\"\",\"currency\":\"\"}')

-- themes/altnum/views/pay-thank-you/index.php
-- themes/altnum/views/pay/index.php
-- themes/altnum/views/admin/package-create/index.php
-- themes/altnum/views/admin/package-update/index.php
-- themes/altnum/views/admin/settings/index.php
-- controller theme also modified

-- app/controllers/Pay.php
-- app/controllers/admin/AdminPackageCreate.php
-- app/controllers/admin/AdminPackageUpdate.php
-- app/controllers/admin/AdminSettings.php

-- vendor/composer/autoload_psr4.php
-- vendor/composer/autoload_static.php
-- vendor/yabacon