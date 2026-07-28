import Ctr from "../../../code/src/mods/ctr.js";
import Currency from "../../../code/src/mods/currency.js";
import { Tyrax } from "../../../code/src/tyrux/main.js";
import { tbl_address } from "../db/tables.js";

const table = "address";

export async function getAllAddress(){
    let result = Tyrax.ctrsync({
        action: "query",
        query: `select * from ${table} where active = 1 order by city,brgy asc`,
        dataOnly: true
    });
    return await result;
}

export function NegrosCode(){
    return "064500000";
}

export async function getAllShippingAddress(){
    let result = await Tyrax.select({
        table: tbl_address(),
        dataOnly: true
    });
    return result;
}

export async function setAddressOnCB(cbid){
    let data = await getAllAddress();
    Ctr.set_html(cbid, ``);
    Ctr.add_html(cbid, `<option value=''>Select Shipping address</option>`)
    for(let d in data){
        let row = data[d];
        Ctr.add_html(cbid, `<option value='${row.id}'>${row.city+ ", " +row.brgy+" "+Currency.peso(row.shipping)}</option>`)
    }
}

export async function getShippingById(id){
    let data = Tyrax.ctrsync({
        action: "select",
        table: table,
        where: {id: id}
    });
    let record = await data;
    return record?.data[0]?.shipping ?? 0;
}

export async function getShippingDetailsById(id){
    let data = Tyrax.ctrsync({
        action: "findOne",
        table: table,
        where: {id: id},
        dataOnly: true
    });
    let record = await data;
    return record;
}