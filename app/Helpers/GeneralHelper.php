<?php
use Illuminate\Support\Facades\DB;

if (!function_exists('is_active_menu')) {
    function is_active_menu(string|array $route): string
    {
        $activeClass = ' active';

        if (is_string($route)) {
            if (request()->is(substr($route . '*', 1))) {
                return $activeClass;
            }

            if (request()->is(str($route)->slug() . '*')) {
                return $activeClass;
            }

            if (request()->segment(2) === str($route)->before('/')) {
                return $activeClass;
            }

            if (request()->segment(3) === str($route)->after('/')) {
                return $activeClass;
            }
        }

        if (is_array($route)) {
            foreach ($route as $value) {
                $actualRoute = str($value)->remove(' view')->plural();

                if (request()->is(substr($actualRoute . '*', 1))) {
                    return $activeClass;
                }

                if (request()->is(str($actualRoute)->slug() . '*')) {
                    return $activeClass;
                }

                if (request()->segment(2) === $actualRoute) {
                    return $activeClass;
                }

                if (request()->segment(3) === $actualRoute) {
                    return $activeClass;
                }
            }
        }

        return '';
    }
}

function is_active_submenu(string|array $route): string
{
    $activeClass = ' submenu-open';

    if (is_string($route)) {
        if (request()->is(substr($route . '*', 1))) {
            return $activeClass;
        }

        if (request()->is(str($route)->slug() . '*')) {
            return $activeClass;
        }

        if (request()->segment(2) === str($route)->before('/')) {
            return $activeClass;
        }

        if (request()->segment(3) === str($route)->after('/')) {
            return $activeClass;
        }
    }

    if (is_array($route)) {
        foreach ($route as $value) {
            $actualRoute = str($value)->remove(' view')->plural();

            if (request()->is(substr($actualRoute . '*', 1))) {
                return $activeClass;
            }

            if (request()->is(str($actualRoute)->slug() . '*')) {
                return $activeClass;
            }

            if (request()->segment(2) === $actualRoute) {
                return $activeClass;
            }

            if (request()->segment(3) === $actualRoute) {
                return $activeClass;
            }
        }
    }

    return '';
}

function cekAssign($company_id, $user_id)
{
    return DB::table('assign_company')
        ->where('company_id', $company_id)
        ->where('user_id', $user_id)
        ->count();
}

if (!function_exists('set_active')) {
    function set_active($uri)
    {
        if (is_array($uri)) {
            foreach ($uri as $u) {
                if (Route::is($u)) { // jika route sekarang sama dengan variable u
                    return 'active';
                }
            }
        } else {
            if (Route::is($uri)) { // jika route sekarang sama dengan variable u
                return 'active';
            }
        }
        // return request()->routeIs($uri) ? 'active' : '';
    }
}

if (!function_exists('formatRupiah')) {
    /**
     * Format number to Indonesian Rupiah (IDR) currency format
     *
     * @param  mixed  $amount  The amount to format
     * @param  bool  $includeSymbol  Whether to include the 'Rp' symbol (default: true)
     * @return string
     */
    function formatRupiah($amount, $includeSymbol = true)
    {
        // Check if the value is numeric
        if (!is_numeric($amount)) {
            return $amount;
        }

        // Format the number with thousands separators and no decimals
        $formattedAmount = number_format($amount, 0, ',', '.');

        // If the $includeSymbol is true, add the 'Rp' symbol in front
        if ($includeSymbol) {
            return 'Rp ' . $formattedAmount;
        }

        return $formattedAmount;
    }
}
