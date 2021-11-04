<script>
    //method for display cart data
    function displayCart() {
        var noImage = '<?php echo asset('/public/product_image/noimage.jpg')?>';
        var url = '{{asset("public/product_image/")}}';
        var package_image = '{{asset('public/package_image/')}}';
        var cartArray = projectOrderCart.listCart();
        var output = "";
        var test = 0;
        for (var i in cartArray) {
            var pro = JSON.stringify(cartArray[i].name);
            var pro11 = JSON.parse(cartArray[i].name);
            var unitJson = JSON.parse(cartArray[i].unit);
            var unit = JSON.stringify(cartArray[i].unit);

            // output += '<input type="hidden" value="' + cartArray[i].unit + '" name="unit[]"><input type="hidden" value="' + cartArray[i].name + '" name="name[]"><input type="hidden" value="' + cartArray[i].product_or_package_id + '" name="product_or_package_id[]"><input type="hidden" value="' + cartArray[i].pprice + '" name="pprice[]"><input type="hidden" value="' + cartArray[i].mrp + '" name="mrp[]"><input type="hidden" value="' + cartArray[i].count + '" name="qty[]"><input type="hidden" value="' + cartArray[i].unit_id + '" name="unit_id[]"><input type="hidden" value="' + cartArray[i].discount + '" name="discount[]"><input type="hidden" value="' + cartArray[i].moq + '" name="moq[]"><input type="hidden" value="' + cartArray[i].type + '" name="type[]">';

            if (test == 0) {
                output += '<div class="container-fluid desktop-view mobile-view"><div class="row cartbox" style="padding-top: 3px;">';
                test = 1;
            } else {
                output += '<div class="container-fluid desktop-view mobile-view"><div class="row cartbox" style="background:#F5F5F6;padding-top: 3px;">';
                test = 0;
            }

            output += '<div class="col-md-2 col-sm-2 col-xs-2 cart_amount_div" style="padding:0px;"><span class="arrow" onclick=\'QtyPlus("'+ cartArray[i].type +'",'+ cartArray[i].product_or_package_id + ','+pro+',' + cartArray[i].pprice + ',' + cartArray[i].mrp + ',' + cartArray[i].discount + ',"' + cartArray[i].image + '",' + cartArray[i].unit_id + ',' + cartArray[i].moq + ','+ unit +')\'><i class="fa fa-chevron-up" aria-hidden="true"></i></span><input type="number" name="" class="cart-qty cart_amount_field" value="' + cartArray[i].count + '" min="0" readonly onblur=""><span class="arrow" onclick=\'QtyMinus("'+ cartArray[i].type +'",'+ cartArray[i].product_or_package_id + ','+pro+',' + cartArray[i].pprice + ',' + cartArray[i].mrp + ',' + cartArray[i].discount + ',"' + cartArray[i].image + '",' + cartArray[i].unit_id + ',' + cartArray[i].moq + ','+ unit +')\'><i class="fa fa-chevron-down" aria-hidden="true"></i></span></div>'
                + '<div class="col-md-9 col-sm-9 col-xs-9" style="padding: 0px;">';
            if (cartArray[i].image == "") {
                output += '<img src="' + noImage + '" height="40" alt="Product Image">';
            } else if(cartArray[i].type == "package"){
                output += '<img src="' + package_image + '/'+cartArray[i].image + '" height="40" alt="Product Image">';
            }
            else {
                output += '<img src="' + url + '/'+cartArray[i].image + '" height="40" alt="Product Image">';
            }
            //shoppingCart.totalCart()


            output += '<div style="margin-left: 5px;float:left !important;width:75% !important;"><p class="cart-product cart-product_title_eclipse">' + pro11.{{App::getLocale()}} + '</p><p class="cart-unit-cart">' + unitJson.{{App::getLocale()}} + ' </p><p class="cart-price">৳ ' + projectOrderCart.getQtyPrice(cartArray[i].type,cartArray[i].product_or_package_id).toFixed(2) + '</p></div></div>'
                + '<div class="col-md-1 col-sm-1 col-xs-1" style="padding:0px;"><span class="cart-cross" onclick="crossItem(\'' + cartArray[i].type + '\',' + cartArray[i].product_or_package_id + ')" data-name="' + cartArray[i].name + '"><i class="far fa-times-circle"></i></span></div>'
                + '</div></div>';
        }

        $('#cart_container').html(output);
        $('#countId').html(projectOrderCart.countItem())
        $('#itemCount').html(projectOrderCart.countItem() + " ITEMS")
        $('#itemCountMobile').html(projectOrderCart.countItem())
        $('#totalItemPrice').html('৳ ' + projectOrderCart.totalCart().toFixed(2))
        $('#allTotal').html('৳ ' + projectOrderCart.totalCart().toFixed(2))

        //console.log(output);

    }


    function test(data){
        alert(data);
    }


    function clearCart() {
        projectOrderCart.clearCart();
    }


    function crossItem(type, product_or_package_id) {
        projectOrderCart.removeItemFromCartAll(type,product_or_package_id);
        displayCart();
        //reloadCartValue(name);
        clearInput(product_or_package_id);
    }

    function clearInput(id) {
        document.getElementById("qtyValue" + id).value = 0;
        document.getElementById("qtyValueMobile" + id).value = 0;
    }



    function QtyPlus(type,product_or_package_id,name,pprice,mrp,discount,image,moq,point,unit_type_discount) {

        let qty = document.getElementById('qtyValue' + product_or_package_id).value;
        let selQuantity_type = document.getElementById('selectUnit_' + product_or_package_id).value;
        let expld = selQuantity_type.split("#");
        if(discount == null){
            discount = 0;
        }
        /*********
         * 0: "quantity_type"  //type
         1: "1"  //quntity
         2: "{\"en\":\"Piece\",\"bn\":\"পিস\"}"  // name
         3: "1" // moq
         4: "1" Moq for Quantity

         0: "unit_type"
         1: "10"
         2: "{\"en\":\"Box\",\"bn\":\"বক্স\"}"
         3: "1"
         4: "1" Moq for Quantity
         */


        // let moq_json = JSON.parse(expld[3]);
        // let moq_latest = moq_json.moq_of_quantity_type;


        //expld[1] unit quantity sonkha
        //expld[0] unit identify type
        //expld[2] unit name
        let puarches_price = pprice;
        let selling_price = mrp;
        let discount_price = discount;
        let points =  (point/expld[4]);
        let moq_latest = expld[3];



        if(expld[0] == "unit_type"){
            puarches_price = ((pprice * expld[1]));
            selling_price = ((mrp * expld[1]));
            discount_price = discount * expld[1];
            points =  (points * expld[1]);
        }

        // if(expld[0] == "micro_quantity_type"){
        //     puarches_price = ((pprice / expld[1]));
        //     selling_price = ((mrp / expld[1]));
        //     discount_price = ((discount / expld[1]));
        // }



        if (qty == "" || qty < 0) {
            qty = 0;
            projectOrderCart.removeItemFromCartAll(type,product_or_package_id);
        } else {
            if(qty < moq_latest){
                qty = parseInt(qty) + parseInt(moq_latest);
            }else{
                qty = parseInt(qty) + parseInt(1);
            }
        }
        document.getElementById('qtyValue' + product_or_package_id).value = qty;

        if(projectOrderCart.haveProductOrNot(type,product_or_package_id,expld[0]) == true){
            projectOrderCart.removeItemFromCartAll(type,product_or_package_id);
            projectOrderCart.addItemToCart(qty,type,product_or_package_id,name,puarches_price,selling_price,discount_price,image,moq_latest,expld[2],expld[0],expld[1],points);
        }else{
            projectOrderCart.addItemToCart(qty,type,product_or_package_id,name,puarches_price,selling_price,discount_price,image,moq_latest,expld[2],expld[0],expld[1],points);
        }
        var cartArray = projectOrderCart.listCart();
        displayCart();
        //alert("action done");
    }


    function QtyMinus(type,product_or_package_id,name,pprice,mrp,discount,image,moq,point) {
        let selQuantity_type = document.getElementById('selectUnit_' + product_or_package_id).value;
        let expld = selQuantity_type.split("#");
        let moq_latest = expld[3];

        if(discount == null){
            discount = 0;
        }


        let qty = document.getElementById('qtyValue' + product_or_package_id).value;

        if(qty == moq_latest){
            qty = parseInt(qty) - parseInt(moq_latest);
        } else if(qty > 0) {
            qty = parseInt(qty) - parseInt(1);
        }else{
            if (qty == "") {
                qty = 0;
            }else if(qty < 0){
                qty = 0;
            }
        }
        document.getElementById('qtyValue' + product_or_package_id).value = qty;


        //expld[1] unit quantity
        //expld[0] unit identify type
        //expld[2] unit name

        let puarches_price = pprice;
        let selling_price = mrp;
        let discount_price = discount;
        let points =  (point/moq);


        if(expld[0] == "unit_type"){
            puarches_price = ((pprice * expld[1]));
            selling_price = ((mrp * expld[1]));
            discount_price = ((discount * expld[1]));
            points =  (points * expld[1]);
        }

        if(expld[0] == "micro_quantity_type"){
            puarches_price = ((pprice / expld[1]));
            selling_price = ((mrp / expld[1]));
            discount_price = ((discount / expld[1]));
        }


        projectOrderCart.updateItemToCart(qty,type,product_or_package_id,name,puarches_price,selling_price,discount_price,image,moq_latest,expld[2],expld[0],expld[1],points);
        displayCart();

        if (qty == 0) {
            projectOrderCart.removeItemFromCartAll(type,product_or_package_id);
            displayCart();
        }
    }


    function orderCountableValue(qty,type,product_or_package_id,name,pprice,mrp,discount,image,moq,point) {


        if(qty == 0){
            projectOrderCart.removeItemFromDirectCart(type,product_or_package_id);
            var cartArray = projectOrderCart.listCart();
            displayCart();
        }

        if(qty != 0 && qty > 0){


            if(discount == null){
                discount = 0;
            }

            let selQuantity_type = document.getElementById('selectUnit_' + product_or_package_id).value;
            let expld = selQuantity_type.split("#");
            //expld[1] unit quantity
            //expld[0] unit identify type
            //expld[2] unit name

            let puarches_price = pprice;
            let selling_price = mrp;
            let discount_price = discount;
            let moq_latest = expld[3];

            if(expld[0] == "unit_type"){
                puarches_price = ((pprice * expld[1]));
                selling_price = ((mrp * expld[1]));
                discount_price = ((discount * expld[1]));
            }

            // if(expld[0] == "micro_quantity_type"){
            //     puarches_price = ((pprice / expld[1]));
            //     selling_price = ((mrp / expld[1]));
            //     discount_price = ((discount / expld[1]));
            // }

            //alert(puarches_price);
            if(projectOrderCart.haveProductOrNot(type,product_or_package_id,expld[0]) == true) {
                projectOrderCart.removeItemFromCartAll(type, product_or_package_id);
                projectOrderCart.addItemToCart(qty,type,product_or_package_id,name,puarches_price,selling_price,discount_price,image,moq_latest,expld[2],expld[0],expld[1],point);
            }else{
                projectOrderCart.updateItemWithChangeValue(qty,type,product_or_package_id,name,puarches_price,selling_price,discount_price,image,moq_latest,expld[2],expld[0],expld[1],point);
            }
            var cartArray = projectOrderCart.listCart();
            displayCart();
        }

    }



    $(document).ready(function () {
        displayCart();
        //clearCart();
    });

</script>



<!--start quick list -->
<div id="quick_list_container" class="quick_list_container_height">
    <div class="quick_list_contianer_header">
        <span><i class="fa fa-briefcase" aria-hidden="true"></i> <span id="countId">0</span> Items</span>
        <button type="button" class="btn btn-danger" id="quick_close"><i class="far fa-times-circle"></i> Close</button>
    </div>

    <!--
    requisition.summery
    wmm.send.requisition
    -->
    {{Form::open(['route'=>['order-checkout.submit',$prefix],'method'=>'GET'])}}
    <div id="cart_container" class="cart_container_scroll" style="padding-top: 10px !important;">



        <div class="">
            asdjflksdajf
        </div>

    </div>

    <div class="cart-footer" style="background: #EBEDEF;color: white;overflow: hidden;">
        <h4 style="float: left;color: #6437A3;font-size: 19px;margin-top: 8px;"><span id="allTotal">0</span></h4>
        <button id="submitRequsition" type="submit" class="checkout-button"
                style="float: right;">Checkout</button>
    </div>
    {{Form::close()}}

</div>
<!--end quick list -->


<!--start quick bag list -->
<div id="item-container">
    <a href="javascript:void(0)" id="expendContainer" class="item-header-link">
        <div class="item-header">
            <h3><i class="fa fa-briefcase" aria-hidden="true"></i></h3>
            <span id="itemCount"> 0 {{__('form.items')}}</span>
        </div>
    </a>
    <p id="totalItemPrice"></p>
</div>
<!--end quick bag list -->


<script type="text/javascript">

    $(document).ready(function () {

        let quickElement = $('#quick_list_container');
        $("#expendContainer").on('click', function () {
            quickElement.show();
        });

        $("#mobileExpendContainer").on('click', function () {
            quickElement.show();
        });

        $("#quick_close").on('click', function () {
            quickElement.hide();
        });

    });


    //submitRequsition

    $(document).ready(function () {
        $("#submitRequsition").on("click", function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var dataJson = JSON.parse(sessionStorage.getItem('shoppingCart'));
            /*      console.log("send...");
                  console.log(dataJson);*/
            var dataString = {data: dataJson};
            //console.log(dataString);
            $.ajax({
                type: "get",
                url: "{{url($prefix.'/submit/requisition')}}",
                data: dataString,
                success: function (data) {
                    console.log(data);
                }
            });
        });

    })
</script>

