import Ctr from "../../../code/src/mods/ctr.js";
import { Tyrax } from "../../../code/src/tyrux/main.js";
import { tbl_category } from "../db/tables.js";

export async function getCategories(){
    return new Promise((resolve, reject)=>{
        Tyrax.ctrql({
            action: "get",
            table: tbl_category(),
            where: {"active":1},
            response: (send)=>{
                resolve(send.data ?? []);
            },
            error: (err)=>{
                alert(err.message ?? "Server error");
                reject([]);
            }
        });
    });
}

export async function getCategoriesBy(where){
    return new Promise((resolve, reject)=>{
        Tyrax.ctrql({
            action: "get",
            table: tbl_category(),
            where: {...where, "active":1},
            response: (send)=>{
                resolve(send.data ?? []);
            },
            error: (err)=>{
                reject([]);
            }
        });
    });
}

export async function displayCategoryOnCB(id){
    let categories = await getCategories();
    Ctr.set_html(id, `<option value=''>Select Category</option>`);
    for(let ct in categories){
        let data = categories[ct];
        Ctr.add_html(id,`<option value='${data.id}'>${data.name}</option>`);
    }
}