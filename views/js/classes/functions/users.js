import { Tyrax } from "../../../code/src/tyrux/main.js";

let table = "user";

export async function getRiders(){
    return await Tyrax.ctrsync({
        action: "select",
        table: table,
        where : {active: 1, role: 3},
        dataOnly: true
    });
}

export async function getRiderName(id){
    let record = await Tyrax.ctrsync({
        action: "findOne",
        table: table,
        where : {id: id},
        dataOnly: true
    });
    return record.fullname ?? null;
}

export async function getUserDetails(id){
    let record = await Tyrax.ctrsync({
        action: "findOne",
        table: table,
        where : {id: id},
        dataOnly: true
    });
    return record;
}
