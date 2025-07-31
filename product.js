const id = new URLSearchParams(window.location.search).get("id");

fetch(`http://localhost/product-api/api/products/${id}`)
  .then(res => res.json())
  .then(p => {
    const detail = document.getElementById("product-detail");
    detail.innerHTML = `
      <h2>${p.name}</h2>
      <p><strong>Brand:</strong> ${p.brand}</p>
      <p><strong>Category:</strong> ${p.category}</p>
      <p><strong>Department:</strong> ${p.department}</p>
      <p><strong>Retail Price:</strong> ₹${p.retail_price}</p>
      <p><strong>SKU:</strong> ${p.sku}</p>
    `;
  })
  .catch(() => {
    document.getElementById("product-detail").innerHTML = "Product not found.";
  });
