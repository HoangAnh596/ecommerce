<?php

use App\Enums\PromotionEnum;

if (!function_exists('convert_price')) {
    function convert_price(string $price = '')
    {
        return str_replace('.', '', $price);
    }
}

if (!function_exists('convert_array')) {
    function convert_array($system = null, $keyword = '', $value = '')
    {
        $temp = [];
        if (is_array($system)) {
            foreach ($system as $key => $val) {
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if (is_object($system)) {
            foreach ($system as $key => $val) {
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }

        return $temp;
    }
}

if (!function_exists('convertDateTime')) {
    function convertDateTime(string $date = '', string $format = 'd/m/Y H:i')
    {
        $carbonDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date);

        return $carbonDate->format($format);
    }
}

if (!function_exists('renderDiscountInformation')) {
    function renderDiscountInformation($promotion)
    {
        if ($promotion->method === PromotionEnum::PRODUCT_AND_QUANTITY) {
            $discountValue = $promotion->discountInformation['info']['discountValue'];
            $discountType = ($promotion->discountInformation['info']['discountType'] == 'percent') ? '%' : 'đ';

            return '<span class="label label-success">' . $discountValue . $discountType . '</span>';
        }

        return '<div><a href="' . route('promotion.edit', $promotion->id) . '">Xem chi tiết</a></div>';
    }
}

if (!function_exists('renderSystemInput')) {
    function renderSystemInput(string $name = '', $systems = null)
    {
        return '<input type="text" 
            name="config[' . $name . ']" 
            value="' . old($name, ($systems[$name]) ?? '') . '" 
            class="form-control" 
            placeholder="">
        ';
    }
}

if (!function_exists('renderSystemImages')) {
    function renderSystemImages(string $name = '', $systems = null)
    {
        return '<input type="text" 
            name="config[' . $name . ']" 
            value="' . old($name, ($systems[$name]) ?? '') . '" 
            class="form-control upload-image" 
            placeholder="">
        ';
    }
}

if (!function_exists('renderSystemTextarea')) {
    function renderSystemTextarea(string $name = '', $systems = null)
    {
        return '<textarea name="config[' . $name . ']" 
            class="form-control system-textarea" 
            placeholder="">' . old($name, ($systems[$name]) ?? '') . '</textarea>';
    }
}

if (!function_exists('renderSystemEditor')) {
    function renderSystemEditor(string $name = '', $systems = null)
    {
        return '<textarea name="config[' . $name . ']" 
            class="form-control system-textarea ck-editor" 
            id="' . $name . '"
            placeholder="">' . old($name, ($systems[$name]) ?? '') . '</textarea>';
    }
}

if (!function_exists('renderSystemLink')) {
    function renderSystemLink(array $item = [], $systems = null)
    {
        return (isset($item['link'])) ? '<a class="system-link" href="' . $item['link']['href'] . '" target="' . $item['link']['target'] . '">' . $item['link']['text'] . '</a>' : '';
    }
}

if (!function_exists('renderSystemTitle')) {
    function renderSystemTitle(array $item = [], $systems = null)
    {
        return (isset($item['title'])) ? '<span class="system-link text-danger">' . $item['title'] . '</span>' : '';
    }
}

if (!function_exists('renderSystemSelect')) {
    function renderSystemSelect(array $item = [], string $name = '', $systems = null)
    {
        $html = '<select name="config[' . $name . ']" class="form-control">';
        foreach ($item['option'] as $key => $value) {
            $html .= '<option value="' . $key . '" ' . ((isset($systems[$name]) && $key == $systems[$name]) ? 'selected' : '') . '>' . $value . '</option>';
        }
        $html .= '</select>';

        return $html;
    }
}

if (!function_exists('recursive')) {
    function recursive($data, $parentId = 0)
    {
        $temp = [];
        if (!is_null($data) && count($data)) {
            foreach ($data as $key => $val) {
                if ($val->parent_id == $parentId) {
                    $temp[] = [
                        'item' => $val,
                        'children' => recursive($data, $val->id)
                    ];
                }
            }
        }
        return $temp;
    }
}

if (!function_exists('write_url')) {
    function write_url(
        string $canonical = '',
        bool $fullDomain = true,
        $suffix = false,
        $externalLink = false
    ) {
        if (strpos($canonical, 'http') !== false) {
            return $canonical;
        }

        $fullUrl = (($fullDomain === true) ? config('app.url') : '')
            . $canonical
            . (($suffix === true) ? config('apps.general.suffix') : '');

        return $fullUrl;
    }
}

if (!function_exists('frontend_recursive_menu')) {
    function frontend_recursive_menu(array $data, int $count = 1, $type = 'html')
    {
        $html = '';
        if (isset($data) && !is_null($data) && count($data)) {
            if ($type == 'html') {
                foreach ($data as $key => $val) {
                    $name  = $val['item']->languages->first()->pivot->name;
                    $canonical = write_url($val['item']->languages->first()->pivot->canonical, true, true);
                    $ulClass = ($count >= 1) ? 'menu-level__' . ($count + 1) : '';

                    $html .= '<li class="' . (($count == 1) ? 'children' : '') . '">';
                    $html .= '<a href="' . $canonical . '" title="' . $name . '">' . $name . '</a>';
                    if (count($val['children'])) {
                        $html .= '<div class="dropdown-menu">';
                        $html .= '<ul class="uk-list uk-clearfix menu-style ' . $ulClass . '">';
                        $html .= frontend_recursive_menu($val['children'], $val['item']->parent_id, $count + 1);
                        $html .= '</ul>';
                        $html .= '</div>';
                    }
                    $html .= '</li>';
                }

                return $html;
            }
        }

        return $data;
    }
}

if (!function_exists('recursive_menu')) {
    function recursive_menu($data)
    {
        $html = '';
        if (count($data)) {
            foreach ($data as $key => $val) {
                $itemId = $val['item']->id;
                $itemName = $val['item']->languages->first()->pivot->name;
                $itemUrl = route('menu.children', ['id' => $itemId]);

                $html .= "<li class='dd-item' data-id='$itemId'>";
                $html .= "<div class='dd-handle'>";
                $html .= "<span class='label label-info'><i class='fa fa-arrows'></i></span> $itemName";
                $html .= "</div>";
                $html .= "<a class='create-children-menu' href='$itemUrl'> Quản lý menu con </a>";
                if (count($val['children'])) {
                    $html .= "<ol class='dd-list'>";
                    $html .= recursive_menu($val['children']);
                    $html .= "</ol>";
                }
                $html .= "</li>";
            }
        }
        return $html;
    }
}

if (!function_exists('biuldMenu')) {
    function biuldMenu($menus = null, $parent_id = 0, $prefix = '')
    {
        $output = [];
        $count = 1;
        if (count($menus)) {
            foreach ($menus as $key => $val) {
                if ($val->parent_id == $parent_id) {
                    $val->position = $prefix . $count;
                    $output[] = $val;
                    $output = array_merge($output, biuldMenu($menus, $val->id, $val->position . '.'));
                    $count++;
                }
            }
        }
        return $output;
    }
}

if (!function_exists('loadClassInterface')) {
    function loadClassInterface(string $model = '', $interface = 'Repository')
    {
        $baseNamespace = $interface === 'Repository'
            ? '\App\Repositories\\'
            : '\App\Services\\';

        $class = $baseNamespace . ucfirst($model) . $interface;

        return class_exists($class) ? app($class) : null;
    }
}

if (!function_exists('convertArrayByKey')) {
    function convertArrayByKey($object = null, $fields = [])
    {
        $temp = [];
        foreach ($object as $key => $val) {
            foreach ($fields as $field) {
                if (is_array($object)) {
                    $temp[$field][] = $val[$field];
                } else {
                    $extract = explode('.', $field);
                    if (count($extract) == 2) {
                        $temp[$extract[0]][] = $val->{$extract[1]}->first()->pivot->{$extract[0]};
                    } else {
                        $temp[$field][] = $val->{$field};
                    }
                }
            }
        }

        return $temp;
    }
}
