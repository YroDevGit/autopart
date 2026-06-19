import CtrDATE from "../../../code/src/mods/date.js";
import { Tyrax } from "../../../code/src/tyrux/main.js";
import { tbl_product } from "../db/tables.js";
import { customer_tbl } from "./customer.js";

let table = "transaction";
let table_details = "transaction_details";

export async function getAllOrders(){
    let result = await Tyrax.ctrsync({
        action: "query",
        query: `select t.id, t.transaction_code 'code', t.rider, t.subtotal, t.shipping 'shippingFee', t.total_price 'total', t.customer_id, c.contact, c.address, c.email,c.fulladdress, t.created_at 'orderDate', t.status, c.fullname 'customerName', c.id 'customer_id' from ${table} t, ${customer_tbl()} c where c.id = t.customer_id order by t.updated_at desc;`,
        dataOnly: true
    });
    let arr = [];
    for(let res in result){
        let row = result[res];
        row = {...row, items: await getOrderDetails(row.code)}
        arr = [...arr,{...row}];
    }
    return arr;
}


export async function getAllOrdersByRider(riderId){
    let result = await Tyrax.ctrsync({
        action: "query",
        query: `select t.id, t.transaction_code 'code', t.rider, t.subtotal, t.shipping 'shippingFee',t.date_delivered, t.total_price 'total', t.customer_id, c.contact, c.address, c.email,c.fulladdress, t.created_at 'orderDate', t.status, c.fullname 'customerName', c.id 'customer_id' from ${table} t, ${customer_tbl()} c where c.id = t.customer_id and t.rider = ${riderId} order by t.updated_at desc;`,
        dataOnly: true
    });
    let arr = [];
    for(let res in result){
        let row = result[res];
        row = {...row, items: await getOrderDetails(row.code)}
        arr = [...arr,{...row}];
    }
    return arr;
}

export async function getOrderDetails(code){
    let result = await Tyrax.ctrsync({
        action: "query",
        query: `select t.id, t.product_id, t.quantity 'qty', t.price, t.total_price, t.transaction_code, t.created_at, p.name, p.details, p.image from ${table_details} t, ${tbl_product()} p where p.id = t.product_id and t.transaction_code = '${code}';`,
        dataOnly: true
    });
    return result;
}

export async function updateStatus(id, status, message = null){
    if(status == 7){
        return Tyrax.ctrsync({
            action: "update",
            table: table,
            update: {status: status, remarks : message??"", updated_at: CtrDATE.now()},
            where: {id : id}
        });
    }else{
        return Tyrax.ctrsync({
            action: "update",
            table: table,
            update: {status: status, updated_at: CtrDATE.now(), date_delivered: CtrDATE.now()},
            where: {id : id}
        });
    }
}

export async function updateDriver(orderid,driver){
    return await Tyrax.ctrsync({
        action: "update",
        table: table,
        where: {id: orderid},
        update: {rider: driver}
    });
}