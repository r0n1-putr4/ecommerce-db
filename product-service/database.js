const { Sequelize } = require("sequelize");

const sequelize = new Sequelize("productdb", "root", "Dev@root23", {
  host: process.env.DB_HOST, // Host database yang akan digunakan
  dialect: "mysql", // Jenis database yang digunakan
  port: 3306, // Port default MySQL
});

module.exports = sequelize;