const express = require("express");
const app = express();
const sequelize = require("./database");
const { DataTypes } = require("sequelize");
const cors = require("cors");
const bodyParser = require("body-parser");

// Middleware untuk parsing JSON
app.use(express.json());
app.use(bodyParser.urlencoded({ extended: true }));

//setting cors
app.use(function (req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header(
    "Access-Control-Allow-Headers",
    "Origin, Content-Type, Accept, Authorization"
  );
  res.header(
    "Access-Control-Allow-Methods",
    "POST, PUT, GET, DELETE, HEAD, OPTIONS"
  );
  next();
});
app.use(cors());

const Product = sequelize.define("Product", {
  name: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  description: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  price: {
    type: DataTypes.FLOAT,
    allowNull: false,
  },
});

// Inisialisasi Database
const initDb = async () => {
  try {
    await sequelize.sync({ alter: true });
    console.log("Products table synced with database");
  } catch (error) {
    console.error("Error creating database tables:", error);
  }
};
initDb();

// Response standar
const successResponse = (res, message, data = null) => {
  res.status(200).json({
    success: true,
    message: message,
    data: data,
  });
};
const errorResponse = (res, status, message) => {
  res.status(status).json({
    success: false,
    message: message,
  });
};

app.get("/", function (req, res) {
  res.json({ message: "Welcome to ExpressJS" });
});

// API untuk mendapatkan semua produk
app.get("/products", async (req, res) => {
  try {
    const products = await Product.findAll();
    successResponse(res, "Products Retrieved Successfully", products);
  } catch (error) {
    console.log(error);
    errorResponse(res, 500, "Error Retrieving Products");
  }
});

app.post("/product", async (req, res) => {
  const { name, description, price } = req.body;
  try {
    if (!name || !price) {
      return errorResponse(res, 400, "Name and Price are required");
    }

    const product = await Product.create({ name, description, price });
    successResponse(res, "Product created successfully", product);
  } catch (error) {
    errorResponse(res, 500, `Error creating product : ${error.message}`);
  }
});

// API untuk mendapatkan produk berdasarkan ID
app.get("/products/:id", async (req, res) => {
  try {
    const id = parseInt(req.params.id);
    const product = await Product.findByPk(id);

    if (!product) {
      return errorResponse(res, 404, "Product Not Found");
    }
    successResponse(res, "Product Retrieved Successfully", product);
  } catch (error) {
    console.log(error);
    errorResponse(res, 500, "Error Retrieving Product");
  }
});

const port = 3000;

app.listen(port, () => {
  console.log(`Server running :  http://localhost:${port} on port ok ${port}`);
});
