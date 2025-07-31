fetch("http://localhost/product-api/api/products")
  .then(res => res.json())
  .then(data => {
    const container = document.getElementById("product-list");
    data.forEach(p => {
      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `
        <h3>${p.name}</h3>
        <p>₹${p.retail_price}</p>
        <a href="product.html?id=${p.id}">View</a>
      `;
      container.appendChild(card);
    });
  })
  .catch(() => {
    document.getElementById("product-list").innerHTML = "Failed to load products.";
  });
