import Ctr from "../../../code/src/mods/ctr.js";
import CtrDATE from "../../../code/src/mods/date.js";
import { Twal } from "../../../code/src/mods/twal.js";
import { Tyrax } from "../../../code/src/tyrux/main.js";
import { tbl_category, tbl_inventory, tbl_product } from "../db/tables.js";

export async function getProducts(product = null, category = null) {
    let sql = `SELECT p.id, COALESCE((SELECT SUM(i.quantity) FROM inventory i WHERE i.product_id = p.id),0) - COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS quantity, COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS sold, p.name, p.details, p.price, p.created_at, p.updated_at, p.category AS category_id, p.image, c.name AS category FROM ${tbl_product()} p, category c WHERE p.category = c.id AND p.active = 1${category ? ` AND c.id = '${category}'` : ""} ORDER BY updated_at DESC LIMIT 21;`;
    if (product) {
        sql = `select p.id,COALESCE((SELECT SUM(i.quantity) FROM inventory i WHERE i.product_id = p.id),0) - COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS quantity, COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS sold, p.name, p.details, p.price, p.created_at, p.updated_at, p.category as 'category_id', p.image, c.name as 'category' from ${tbl_product()} p, category c where p.category = c.id and p.active = 1${category ? ` AND c.id = '${category}'` : ""} and (p.name like '%${product}%' or p.details like '%${product}%') order by updated_at desc limit 21;`;
    }

    let records = await Tyrax.ctrsync({
        action: "query",
        query: sql,
        dataOnly: true
    });
    return records;
}


export async function getProductLeft(product) {
    let sql = `SELECT p.id, COALESCE((SELECT SUM(i.quantity) FROM inventory i WHERE i.product_id = p.id),0) - COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS quantity, COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS sold, p.name, p.details, p.price, p.created_at, p.updated_at, p.category AS category_id, p.image, c.name AS category FROM ${tbl_product()} p, category c WHERE p.category = c.id AND p.active = 1 ORDER BY updated_at DESC LIMIT 5;`;
    if (product) {
        sql = `select p.id,COALESCE((SELECT SUM(i.quantity) FROM inventory i WHERE i.product_id = p.id),0) - COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS quantity, COALESCE((SELECT SUM(t.quantity) FROM transaction_details t WHERE t.product_id = p.id),0) AS sold, p.name, p.details, p.price, p.created_at, p.updated_at, p.category as 'category_id', p.image, c.name as 'category' from ${tbl_product()} p, category c where p.category = c.id and p.active = 1 and p.id = ${product} order by updated_at desc limit 5;`;
    }

    let records = await Tyrax.ctrsync({
        action: "query",
        query: sql,
        dataOnly: true
    });
    return records[0].quantity;
}

export async function getStocks(id) {
    let result = await Tyrax.ctrsync({
        action: "query",
        query: `select sum(quantity)'count' from ${tbl_inventory()} where product_id = ${id}`,
        dataOnly: true
    });
    return result[0]?.count ?? 0;
}

export async function productUpdated(id) {
    return new Promise((resolve, reject) => {
        Tyrax.ctrql({
            action: "update",
            table: tbl_product(),
            where: { id: id },
            update: { updated_at: CtrDATE.now() },
            response: (send) => {
                resolve(send);
            }
        });
    });
}

export async function disableProduct(id) {
    return await Tyrax.ctrsync({
        table: tbl_product(),
        action: "update",
        where: { id: id },
        update: { active: 0 }
    })
}

export async function addProducts(data){
    return new Promise((resolve, reject)=>{
        Tyrax.post({
            url: "transaction/add",
            data: data,
            loading: true,
            //test: true,
            response: (send)=>{
                resolve(send);
            },
            error: (err)=>{
                Twal.err(err?.message ?? "Server error");
                reject(err);
            }
        });
    });
}


