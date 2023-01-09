<?php
declare(strict_types = 1);

class View_Render_Service
{
    public static function renderView(string $file_name): void
    {
        ob_start();

        include_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file_name . '.php';

        $template = ob_get_contents();

        ob_end_clean();

        echo $template;
    }
}