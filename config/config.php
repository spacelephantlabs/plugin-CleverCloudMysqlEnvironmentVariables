<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

return array(
    'Piwik\Config' => DI\decorate(function ($previous, \Psr\Container\ContainerInterface $c) {
        $settings = $c->get(\Piwik\Application\Kernel\GlobalSettingsProvider::class);

        $ini = $settings->getIniFileChain();
        $all = $ini->getAll();
        foreach ($all as $category => $settings) {
            $categoryEnvName = 'MATOMO_' . strtoupper($category);
            $general = $previous->$category;
            foreach ($settings as $settingName => $value) {
                $settingEnvName  = $categoryEnvName . '_' .strtoupper($settingName);

                $envValue = getenv($settingEnvName);
                if ($envValue !== false) {
                    $general[$settingName] = $envValue;
                    $previous->$category = $general;
                }
            }
            //Set MySQL Credentials from clever cloud environment variables
            if ($category === "database") {
              $general['host'] = getenv('MYSQL_ADDON_HOST');
              $general['username'] = getenv('MYSQL_ADDON_USER');
              $general['password'] = getenv('MYSQL_ADDON_PASSWORD');
              $general['dbname'] = getenv('MYSQL_ADDON_DB');
              $general['tables_prefix'] = 'matomo_';
              $general['port'] = intval(getenv('MYSQL_ADDON_PORT'));

              $previous->$category = $general;
          }
        }

        return $previous;
    }),
);
