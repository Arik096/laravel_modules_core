// ************************************************
// Shopping Cart API
// ************************************************

var projectOrderCart = (function() {
    // =============================
    // Private methods and propeties
    // =============================
    cartOrderArray = [];
    // Constructor
    function Item(count,type,product_or_package_id,name,pprice,mrp,discount,image,moq,unit = null,unit_identify = null,unit_quantity = 1,point = 0) {
        this.count = count;
        this.type = type;
        this.product_or_package_id = product_or_package_id;
        this.name = name;
        this.pprice = pprice;
        this.mrp = mrp;
        this.discount = discount;
        this.image = image;
        this.unit_identify = unit_identify;
        this.moq = moq;
        this.unit = unit;
        this.unit_quantity = unit_quantity;
        this.point = point;
    }

    // Save cart
    function saveCart() {
        sessionStorage.setItem('projectOrderCart', JSON.stringify(cartOrderArray));
    }

    // Load cart
    function loadCart() {
        cartOrderArray = JSON.parse(sessionStorage.getItem('projectOrderCart'));
    }

    if (sessionStorage.getItem("projectOrderCart") != null) {
        loadCart();
    }


    // =============================
    // Public methods and propeties
    // =============================
    var obj = {};

    // Add to cart
    obj.addItemToCart = function(count,type,product_or_package_id,name,pprice,mrp,discount,image,moq,unit,unit_identify,unit_quantity,point) {
        for(var item in cartOrderArray) {
            if(cartOrderArray[item].type == type && cartOrderArray[item].product_or_package_id == product_or_package_id) {
                cartOrderArray[item].count = parseInt(count);
                saveCart();
                return;
            }
        }
        var item = new Item(count,type,product_or_package_id,name,pprice,mrp,discount,image,moq,unit,unit_identify,unit_quantity,point);
        cartOrderArray.push(item);
        saveCart();
    }

    obj.getQty = function(type,id) {
        var qty = 0;
        for(var i in cartOrderArray) {
            if (cartOrderArray[i].type == type && cartOrderArray[i].product_or_package_id == id) {
                qty = cartOrderArray[i].count;
                break;
            }
        }
        return qty;
    }


    obj.haveProductOrNot = function(type,id,unit) {
        for(var i in cartOrderArray) {
            if (cartOrderArray[i].type == type && cartOrderArray[i].product_or_package_id == id && cartOrderArray[i].unit_identify != unit) {
                return true;
            }
        }
    }



    obj.getPoint = function() {
        var qty = 0;
        var point = 0;
        var total_point = 0;
        for(var i in cartOrderArray) {
            total_point += (cartOrderArray[i].count * cartOrderArray[i].point);
            // qty = cartOrderArray[i].count;
            // point = cartOrderArray[i].point;
        }
        return total_point;
    }

    obj.getQtyPrice = function(type,product_or_package_id) {
        var qty = 0;
        for(var i in cartOrderArray) {
            if (cartOrderArray[i].type == type && cartOrderArray[i].product_or_package_id == product_or_package_id) {
                qty = cartOrderArray[i].count;
                pay = cartOrderArray[i].pprice;
                break;
            }
        }
        return qty * pay;
    }



    obj.totalMrp = function() {
        var totalCart = 0;
        for(var item in cartOrderArray) {
            totalCart += cartOrderArray[item].mrp * cartOrderArray[item].count;
        }

        return Number(totalCart.toFixed(2));
    }



    // Count cart
    obj.totalCount = function() {
        var totalCount = 0;
        for(var item in cartOrderArray) {
            totalCount += cartOrderArray[item].count;
        }
        return totalCount;
    }



// Add to cart
    obj.updateItemToCart = function(count,type,product_or_package_id,name,pprice,mrp,discount,image,moq,unit,unit_identify,unit_quantity,point) {
        for(var item in cartOrderArray) {
            if(cartOrderArray[item].name === name) {
                cartOrderArray[item].count -= 1;
                saveCart();
                return;
            }
        }
        var item = new Item(count,type,product_or_package_id,name,pprice,mrp,discount,image,moq,unit,unit_identify,unit_quantity,point);
        cartOrderArray.push(item);
        saveCart();
    }




// Add to cart
    obj.updateItemWithChangeValue = function(count,type,product_or_package_id,name,pprice,mrp,discount,image,moq,unit,unit_identify,unit_quantity,point) {
        for(var item in cartOrderArray) {
            if(cartOrderArray[item].type === type && cartOrderArray[item].product_or_package_id === product_or_package_id) {
                cartOrderArray[item].count = count;
                saveCart();
                return;
            }
        }
        var item = new Item(count,type,product_or_package_id,name,pprice,mrp,discount,image,moq,unit,unit_identify,unit_quantity,point);
        cartOrderArray.push(item);
        saveCart();
    }



    // Set count from item
    obj.setCountForItem = function(name, count) {
        for(var i in cartOrderArray) {
            if (cartOrderArray[i].name === name) {
                cartOrderArray[i].count = count;
                break;
            }
        }
    }


    // Remove item from cart
    obj.removeItemFromCart = function(name) {
        for(var item in cartOrderArray) {
            if(cartOrderArray[item].name === name) {
                cartOrderArray[item].count --;
                if(cartOrderArray[item].count === 0) {
                    cartOrderArray.splice(item, 1);
                }
                break;
            }
        }
        saveCart();
    }



    // Remove item from cart
    obj.removeItemFromDirectCart = function(name) {
        for(var item in cartOrderArray) {
            if(cartOrderArray[item].name === name) {
                //cart[item].count --;
                cartOrderArray.splice(item, 1);
                break;
            }
        }
        saveCart();
    }




    // Remove all items from cart
    obj.removeItemFromCartAll = function(type,id) {
        for(var item in cartOrderArray) {
            if(cartOrderArray[item].type === type && cartOrderArray[item].product_or_package_id === id) {
                cartOrderArray.splice(item, 1);
                break;
            }
        }
        saveCart();
    }


    // Clear cart
    obj.clearCart = function() {
        cartOrderArray = [];
        saveCart();
    }



    // Count Item
    obj.countItem = function() {
        var totalCount = cartOrderArray.length;
        return totalCount;
    }





    // Total cart
    obj.totalCart = function() {
        var totalCart = 0;
        for(var item in cartOrderArray) {
            totalCart += cartOrderArray[item].pprice * cartOrderArray[item].count;
        }
        return Number(totalCart.toFixed(2));
    }

    obj.totalDiscount = function() {
        var totalDis = 0;
        for(var item in cartOrderArray) {
            totalDis += cartOrderArray[item].discount * cartOrderArray[item].count;
        }
        return Number(totalDis.toFixed(2));
    }


    // List cart
    obj.listCart = function() {
        var cartCopy = [];
        for(i in cartOrderArray) {
            item = cartOrderArray[i];
            itemCopy = {};
            for(p in item) {
                itemCopy[p] = item[p];
            }
            itemCopy.total = Number(item.pprice * item.count).toFixed(2);
            cartCopy.push(itemCopy)
        }
        return cartCopy;
    }

    return obj;
})();
