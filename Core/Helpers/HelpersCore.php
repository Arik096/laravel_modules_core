<?php


namespace Modules\Core\Helpers;


class HelpersCore
{

    /**
     * @param $priceJson
     * @return mixed
     * this method use for get just wmm price //use from manual order panel
     */
    public function getPrice($priceJson){
        $json = json_decode($priceJson, true);
        return $json['wmm'];
    }


    /**
     * @param $array
     * @param $flag
     * @return int|mixed
     * this method use for just wmm product discount //use from manual order panel
     */
    function discount($array)
    {
        $dis = $array->where('flag', '=', 4)->first();
        if (!is_null($dis)) {
            $valid_date = $dis->created_at;
            $now = \Carbon\Carbon::now();
            $days = $now->diffInDays($valid_date);
            if ($days <= $dis->valid_till) {
                return $dis->total_discount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }



    /**
     * @param $array
     * @param $flag
     * @return int|mixed
     * product discount for Unit Type  //this method use for just wmm product discount //use from manual order panel
     */
    function UnitTypeDiscount($array)
    {
        $dis = $array->where('flag', '=', 4)->first();
        if (!is_null($dis)) {
            $valid_date = $dis->created_at;
            $now = \Carbon\Carbon::now();
            $days = $now->diffInDays($valid_date);
            if ($days <= $dis->valid_till) {
                return $dis->total_discount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }


    /**
     * @param $array
     * @param $user_id
     * @param $product
     * @return string
     */
    function showStockUsingUnitManagement($array, $user_id, $product)
    {
        $data = $array->where('user_id', '=', $user_id)->first();
        if (!is_null($data)) {

            if ($data->productRetail->micro_quantity_type != "") {
                return $data->number_of_unit_type_qty . ' ' . $data->productRetail->unit_type . ' - ' . $data->number_of_quantity_type_qty . ' ' . $data->productRetail->quantity_type . ' - ' . $data->number_of_micro_quantity_type_qty . " " . $data->productRetail->micro_quantity_type;
            } else {
                return $data->number_of_unit_type_qty . ' ' . $data->productRetail->unit_type . ' - ' . $data->number_of_quantity_type_qty . ' ' . $data->productRetail->quantity_type;
            }
        } else {
            if ($product->micro_quantity_type != "") {
                return '0 ' . isJsonData($product->unit_type) . ' - ' . '0 ' . isJsonData($product->quantity_type) . ' - ' . "0 " . isJsonData($product->micro_quantity_type);
            } else {
                return '0 ' . isJsonData($product->unit_type) . ' - ' . '0 ' . isJsonData($product->quantity_type);
            }
        }
    }





}
