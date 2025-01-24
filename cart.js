let cart = [];
let total = 0;
let selectedSizes = {};

document.addEventListener('DOMContentLoaded', () => {
    loadCartFromStorage();
    updateCart();
    updateCartCount();
});
function goToCart() {
    window.location.href = 'cart.php';
}
// შესვლის შეტყობინების ჩვენება არაავტორიზებული მომხმარებლებისთვის
function showLoginNotification() {
    console.log('showLoginNotification called');
    const notification = document.getElementById('notification');
    if (!notification) {
        console.error('Notification element not found');
        return;
    }
    notification.innerText = 'გთხოვთ გაიაროთ ავტორიზაცია პროდუქტის დასამატებლად';
    notification.style.backgroundColor = '#d32f2f';
    notification.style.display = 'block';
    notification.style.opacity = '1';

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 500);
    }, 3000);

    setTimeout(() => {
        window.location.href = 'login.php';
    }, 2000);
}
//შეტყობინება უკვე ავტორიზებული მომხმარებლისთვის 
function showCartEnabledNotification() {
    const notification = document.getElementById('notification');
    notification.innerText = 'ახლა უკვე შეგიძლიათ დაამატოთ პროდუქტი კალათაში!';
    notification.style.backgroundColor = '#4CAF50';
    notification.style.display = 'block';
    notification.style.opacity = '1';

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 500);
    }, 4000);
}
// ამოწმებს არის თუ არა მომხმარებელი შესული
async function checkLoginStatus() {
    try {
        const response = await fetch('check_session.php');
        const data = await response.json();
        return data.loggedIn;
    } catch (error) {
        console.error('Error checking login status:', error);
        return false;
    }
}
//addToCart ფუნქცია ერთიდაიგივე პროდუქტისა და ზომის მრავალრიცხოვანი რაოდენობის დასამუშავებლად
async function addToCart(name, price, imgSrc) {
    const isLoggedIn = await checkLoginStatus();
   
    if (!isLoggedIn) {
        showLoginNotification();
        return; 
    } 

    // ამოწმებს არჩეული არის თუ არა პროდუქტის ზომა
    const productElement = document.querySelector(`[onclick="addToCart('${name}', ${price}, '${imgSrc}')"]`);
    const sizeOptions = productElement?.closest('.T_shirt_product')?.querySelector('.size-options');
    if (sizeOptions) {
        const selectedSize = selectedSizes[name];
        if (!selectedSize) {
            alert('გთხოვთ, აირჩიოთ ზომა პროდუქტის დამატებამდე.');
            return;
        }

        name = `${name} (${selectedSize})`;  
    }
    // ამოწმებს არჩეული პროდუქტი თუ უკვე არის კალათაში 
    const existingProductIndex = cart.findIndex(item => item.name === name && item.imgSrc === imgSrc);
    if (existingProductIndex !== -1) {
        // თუ პროდუტი უკვე არის კალათაში ის აახლებს რაოდენობას,ფასს და აახლებს კალათას
        cart[existingProductIndex].quantity += 1;
        cart[existingProductIndex].price += price;  
    } else {
        
        cart.push({ name, price, imgSrc, quantity: 1 });
    }
    total += price; 
    saveCartToStorage();
    updateCartCount();
    showNotification('პროდუქტი დამატებულია კალათაში!');

    // ამ კოდით იგზავნება მონაცემები, მონაცემთა ბაზაში
    try {
        const response = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, price, imgSrc })
        });

        const result = await response.json();
        if (response.ok) {
            showNotification(result.message);
        } else {
            showNotification(result.message);
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('დაფიქსირდა შეცდომა პროდუქტის დამატებისას.');
    }
}

// ფუნქცია პროდუქტის ზომის არჩევისთვის
function selectSize(element, size) {
    const shirtName = element.closest('.T_shirt_product').querySelector('.club_name').innerText;
    const options = element.parentElement.querySelectorAll('.size-option');
    options.forEach((option) => option.classList.remove('selected'));
    element.classList.add('selected');
    selectedSizes[shirtName] = size; 
}


function showNotification(message, color = '#4CAF50', icon = '✅') {
    const notification = document.getElementById('notification');
    notification.innerHTML = `<span>${icon}</span> ${message}`;
    notification.style.backgroundColor = color;
    notification.style.display = 'block';
    notification.style.opacity = '1';

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 500);
    }, 3000);
}

// კალათის გაახლება
function updateCart() {
    const cartItems = document.getElementById('cartItems');
    cartItems.innerHTML = ''; 

    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.classList.add('cart-item');
        li.innerHTML = `
            <img src="${item.imgSrc}" alt="${item.name}">
            <div class="details">
                <span>${item.name}</span>
                <span>${item.price.toFixed(2)}₾</span>
                <span>რაოდენობა: ${item.quantity}</span> <!-- Show quantity -->
            </div>
            <button onclick="removeFromCart(${index})">წაშლა</button>
        `;
        cartItems.appendChild(li);
    });

    // Update the total
    document.getElementById('total').innerText = `${total.toFixed(2)}₾`;
}

// კალათაში არსებული პროდუქტების რაოდენობის განახლება
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    cartCount.innerText = cart.length; 
}

// Save cart data to local storage
function saveCartToStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
    localStorage.setItem('total', total.toString());
}

// კალათის მონაცემების დამახსოვრება
function loadCartFromStorage() {
    const storedCart = localStorage.getItem('cart');
    const storedTotal = localStorage.getItem('total');
    if (storedCart) {
        cart = JSON.parse(storedCart);
    }
    if (storedTotal) {
        total = parseFloat(storedTotal);
    }
    updateCart();
    updateCartCount(); 
}

//თითო პროდუქტის წაშლის ფუნქცია
function removeFromCart(index) {
    cart.splice(index, 1); 
    total = cart.reduce((sum, item) => sum + item.price, 0); // მთლიანი ფასის განახლება
    saveCartToStorage();
    updateCart();
    updateCartCount();
}

// კალათის გასუფთავება
function clearCart() {
    cart = [];
    total = 0;
    updateCart();
    saveCartToStorage();
    updateCartCount(); 
}

// შეკვეთის გაკეთების ფუნქცია
async function checkout() {
    if (cart.length === 0) {
        alert("კალათა ცარიელია.");
        return;
    }

    try {
        const response = await fetch('save_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart })
        });

        const result = await response.json();
        if (response.ok) {
            clearCart(); // Clear cart after successful checkout
            alert(result.message || "შეკვეთა წარმატებით განხორციელდა!");
        } else {
            alert(result.message || "დაფიქსირდა შეცდომა შეკვეთის დამუშავებისას.");
        }
    } catch (error) {
        console.error('Error processing the order:', error);
        alert("დაფიქსირდა შეცდომა შეკვეთის დამუშავებისას.");
    }
}

//ნათელი და მუქი ფერების ცვლილება საიტზე
document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const body = document.body;

   
    const darkModeEnabled = localStorage.getItem('dark-mode') === 'true';

    if (darkModeEnabled) {
        body.classList.add('dark-mode');
    }

    darkModeToggle.innerHTML = darkModeEnabled ? '🌙' : '🌞'; 
    darkModeToggle.style.fontSize = '2.5rem';
    darkModeToggle.style.background = 'none';
    darkModeToggle.style.border = 'none';
    darkModeToggle.style.padding = '0';
    darkModeToggle.style.cursor = 'pointer';
  
    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const isDarkMode = body.classList.contains('dark-mode');
             
        darkModeToggle.innerHTML = isDarkMode ? '🌙' : '🌞';
       
        localStorage.setItem('dark-mode', isDarkMode);
    });
});






// მესიჯის გამოტანა როცა უკვე მომხმარებელი დარეგისტრირდება და შეეძლება კალათაში პროდუქტის დამატება
function showCartEnabledNotification() {
    const notification = document.getElementById('notification');
    if (!notification) {
        console.error("Notification element not found");
        return;
    }

    notification.innerText = 'ახლა უკვე შეგიძლიათ დაამატოთ პროდუქტი კალათაში!';
    notification.style.display = 'block';
    setTimeout(() => {
        notification.style.opacity = '1';
    }, 10);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 500);
    }, 4000);
}

// Check session status on page load
window.addEventListener('DOMContentLoaded', () => {
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn) {
                showCartEnabledNotification();
            }
        })
        .catch(error => console.error('Error checking session:', error));
});
//ზომის გამოთვლა
function calculateSize() {
    const height = parseInt(document.getElementById("height").value);
    const weight = parseInt(document.getElementById("weight").value);
    let size = "";
    if (height < 160) {
        if (weight < 55) {
            size = "S";
        } else if (weight < 65) {
            size = "M";
        } else {
            size = "L";
        }
    } else if (height < 175) {
        if (weight < 70) {
            size = "M";
        } else if (weight < 80) {
            size = "L";
        } else {
            size = "XL";
        }
    } else {
        if (weight < 85) {
            size = "L";
        } else if (weight < 100) {
            size = "XL";
        } else if (weight < 115) {
            size = "2XL";
        } else {
            size = "3XL";
        }
    }
    document.getElementById("result").textContent = "თქვენი ტანსაცმლის ზომა: " + size;
}
function navigatePage() {
    const inputText = document.getElementById("search-bar").value.toLowerCase().trim();
    
    // Map combined search terms to pages
    const combinedPages = {
        "ფეხბურთი": "Football.html",
        "ფეხბურთის ბურთი": "burtebi.html",
        "ფეხბურთის ბურთები": "burtebi.html",
        "ფეხბურთის მაისური":"formebi.html",
        "ფეხბურთის მაისურები":"formebi.html",
        "ფეხბურთის ბუცი": "bucebi.html",
        "ფეხბურთის ბუცები": "bucebi.html",
        "ფეხბურთის ხელთათმანები": "mekare.html",
        "ფეხბურთის ხელთათმანი": "mekare.html",
        "კალათბურთი": "basketball.html",
        "კალათბურთის მაისურები": "formebi_Bask.html",
        "კალათბურთის მაისური":"formebi_Bask.html",
        "კალათბურთის ბოტასები": "Botasebi_Bask.html",
        "კალათბურთის ბოტასი": "Botasebi_Bask.html",
        "კალათბურთის ბურთი": "basketball_ball.html",
        "კალათბურთის ბურთები":"basketball_ball.html",
        "რაგბი": "Rugby.html",
        "რაგბის მაისურები": "Rugby_T-shirts.html",
        "რაგბის მაისური": "Rugby_T-shirts.html",
        "რაგბის ბურთები": "Rugby-Burtebi.html",
        "რაგბის ბურთი": "Rugby-Burtebi.html",
        "რაგბის ბუცები": "Rugby_bucebi.html",
        "რაგბის ბუცი": "Rugby_bucebi.html",
        "ბოქსი": "boxing.html",
        "ბოქსის გრუშები": "boxing-bags.html",
        "ბოქსის გრუშა": "boxing-bags.html",
        "ბოქსის ხელთათმანები": "boxing-gloves.html",
        "ბოქსის ხელთათმანი": "boxing-gloves.html",
        "ბოქსის კაპები": "boxing-mouthguards.html",
        "ბოქსის კაპი":"boxing-mouthguards.html",
        "ბოქსის ჩაფხუტები": "boxing-helmets.html",
        "ბოქსის ჩაფხუტი": "boxing-helmets.html",
        "ბოქსის ფეხსაცმელი": "boxing-shoes.html",
        "ბოქსის ფეხსაცმელები": "boxing-shoes.html",
        "ჩოგბურთი":"tennis.html",
        "ჩოგბურთის ჩოგნები": "tennis-rackets.html",
        "ჩოგბურთის ჩოგანი": "tennis-rackets.html",
        "ჩოგბურთის ბურთები": "tennis-balls.html",
        "ჩოგბურთის ბურთი": "tennis-balls.html",
    };

    // ამოწმებს, შეესაბამება თუ არა შეყვანილი ტექსტი რომელიმე კომბინირებულ ტერმინს
    if (combinedPages[inputText]) {
        window.location.href = combinedPages[inputText];
    } else {
        alert("შეყვანილი ინფორმაცია ვერ მოიძებნა! გთხოვთ, შეამოწმოთ თქვენი ჩანაწერი.");
    }
}
