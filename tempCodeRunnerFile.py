import random

for i in range(11, 41):
    discount_amount = random.randint(1, 3)
    print(f"INSERT INTO DISCOUNT (DISCOUNT_ID, DISCOUNT_AMOUNT, DISCOUNT_PERCENT, USER_ID, PRODUCT_ID) VALUES (DISCOUNT_ID_SEQ.NEXTVAL, {discount_amount}, NULL, 10, {i});")
