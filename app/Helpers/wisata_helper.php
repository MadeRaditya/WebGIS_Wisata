<?php

if (!function_exists('get_wisata_image')) {
    function get_wisata_image($path)
    {
        if (empty($path)) {
            return base_url('assets/img/placeholder.png');
        }

        if (strpos($path, 'http') === 0) {
            return $path;
        }

        $localPath = FCPATH . 'assets/uploads/' . $path;
        if (file_exists($localPath)) {
            return base_url('assets/uploads/' . $path);
        }

        return base_url('assets/img/placeholder.png');
    }
}
