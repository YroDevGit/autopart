import { Tyrax } from "../../../code/src/tyrux/main.js";

let table = "customer";

export function customer_tbl(){
    return table;
}

export async function getCustomerDetails(id){
    let result = await Tyrax.ctrsync({
        action: "findOne",
        table: table,
        where: {id: id},
        dataOnly: true
    });

    return result;
}