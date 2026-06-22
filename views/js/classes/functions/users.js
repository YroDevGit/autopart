import { Twal } from "../../../code/src/mods/twal.js";
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

export async function getAllUsers(){
    return await Tyrax.ctrsync({
        action: "select",
        table: table,
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

export async function deleteUser(id, act){
    let newAct = act == 1 ? 0 : 1;
    let res = await Tyrax.ctrsync({
        table: table,
        action: 'update',
        where: {id: id},
        update: {active: newAct}
    });

    if(act == 1){
        Twal.ok("User has been deactivated", true);
    }else{
        Twal.ok("User has been restored", true);
    }
}
