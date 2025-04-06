<?php

if (!function_exists('getPriorityClass')) {
    /**
     * Proje önceliğine göre renk sınıfını döndürür
     * 
     * @param string $priority Proje önceliği
     * @return string Bootstrap renk sınıfı
     */
    function getPriorityClass($priority)
    {
        switch ($priority) {
            case 'Acil':
                return 'table-danger';
            case 'Yüksek':
                return 'table-warning';
            case 'Orta':
                return 'table-info';
            case 'Düşük':
                return 'table-success';
            default:
                return '';
        }
    }
}

if (!function_exists('getStatusClass')) {
    /**
     * Proje durumuna göre renk sınıfını döndürür
     * 
     * @param string $status Proje durumu
     * @return string Bootstrap renk sınıfı
     */
    function getStatusClass($status)
    {
        switch ($status) {
            case 'Ödeme Bekliyor':
                return 'bg-danger text-white';
            case 'Başlamadı':
                return 'bg-secondary text-white';
            case 'Devam Ediyor':
                return 'bg-primary text-white';
            case 'Beklemede':
                return 'bg-warning text-white';
            case 'Tamamlandı':
                return 'bg-success text-white';
            default:
                return '';
        }
    }
}

if (!function_exists('getPrioritySelectClass')) {
    function getPrioritySelectClass($priority) {
        switch ($priority) {
            case 'Acil':
                return 'text-danger bg-danger bg-opacity-10';
            case 'Yüksek':
                return 'text-warning bg-warning bg-opacity-10';
            case 'Orta':
                return 'text-info bg-info bg-opacity-10';
            case 'Düşük':
                return 'text-success bg-success bg-opacity-10';
            default:
                return '';
        }
    }
}

if (!function_exists('getStatusSelectClass')) {
    function getStatusSelectClass($status) {
        switch ($status) {
            case 'Ödeme Bekliyor':
                return 'text-danger bg-danger bg-opacity-10';
            case 'Başlamadı':
                return 'text-secondary bg-secondary bg-opacity-10';
            case 'Devam Ediyor':
                return 'text-primary bg-primary bg-opacity-10';
            case 'Beklemede':
                return 'text-warning bg-warning bg-opacity-10';
            case 'Tamamlandı':
                return 'text-success bg-success bg-opacity-10';
            default:
                return '';
        }
    }
}

if (!function_exists('getPriorityRowClass')) {
    function getPriorityRowClass($priority) {
        switch ($priority) {
            case 'Acil':
                return 'table-danger';
            case 'Yüksek':
                return 'table-warning';
            case 'Orta':
                return 'table-info';
            case 'Düşük':
                return 'table-success';
            default:
                return '';
        }
    }
}

if (!function_exists('getStatusRowClass')) {
    function getStatusRowClass($status) {
        switch ($status) {
            case 'Ödeme Bekliyor':
                return 'table-danger';
            case 'Başlamadı':
                return 'table-secondary';
            case 'Devam Ediyor':
                return 'table-primary';
            case 'Beklemede':
                return 'table-warning';
            case 'Tamamlandı':
                return 'table-success';
            default:
                return '';
        }
    }
}

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status)
    {
        switch ($status) {
            case 'Tamamlandı':
                return 'success';
            case 'Devam Ediyor':
                return 'primary';
            case 'Beklemede':
                return 'warning';
            case 'Ödeme Bekliyor':
                return 'danger';
            case 'Başlamadı':
                return 'secondary';
            default:
                return 'info';
        }
    }
}



