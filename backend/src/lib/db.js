import mysql from 'mysql2/promise';

const dbConfig = {
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'chatapp',
  waitForConnections: true,
  connectionLimit: 10, 
  queueLimit: 0,
};

const pool = mysql.createPool(dbConfig);

console.log('MySQL pool created with connectionLimit:', dbConfig.connectionLimit);

export default pool;
