import { Tyrax } from "../../../code/src/tyrux/main.js";

let table = "photo";

export async function getUploads(){
    let data = await Tyrax.ctrsync({
        action: "select",
        table: table,
        where: {active: 1},
        dataOnly: true
    });

    let arr = [];

    for(let d in data){
        let row = data[d];
        arr[d] = {
            id: row.id,
            name: row.alt,
            original_name: row.alt,
            url: row.path,
            size: row.size,
            uploaded_at: row.created_at
        };
    }
    return arr;
}