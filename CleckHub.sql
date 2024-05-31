DROP TABLE USER_CLECK CASCADE CONSTRAINTS;

DROP TABLE SHOP CASCADE CONSTRAINTS;

DROP TABLE PRODUCT CASCADE CONSTRAINTS;


DROP TABLE WISHLIST CASCADE CONSTRAINTS;

DROP TABLE PRODUCT_WISHLIST CASCADE CONSTRAINTS;

DROP TABLE DISCOUNT CASCADE CONSTRAINTS;

DROP TABLE REVIEW CASCADE CONSTRAINTS;

DROP TABLE CART CASCADE CONSTRAINTS;

DROP TABLE CART_PRODUCT CASCADE CONSTRAINTS;

DROP TABLE ORDERS CASCADE CONSTRAINTS;

DROP TABLE ORDERS_DETAILS CASCADE CONSTRAINTS;

DROP TABLE COLLECTION_SLOT CASCADE CONSTRAINTS;

DROP TABLE PAYMENT CASCADE CONSTRAINTS;

DROP TABLE TRADER CASCADE CONSTRAINTS;

DROP TABLE REVIEW_ACCESS CASCADE CONSTRAINTS;

DROP TABLE REVENUE CASCADE CONSTRAINTS;

--------------------------------------------------------------------------------------------------------------------------------------------


-- Create a Database table to represent the "USER_CLECK" entity.
CREATE TABLE USER_CLECK (
	USER_ID   INTEGER, 
	FIRST_NAME    VARCHAR2(255), 
	LAST_NAME     VARCHAR2(255), 
	ROLE          VARCHAR(32),
    UUSER_NAME VARCHAR2(255),
	EMAIL_ADDRESS   VARCHAR(32),
	GENDER        VARCHAR2(255), 
	DOB     DATE, 
	PHONE_NUMBER    INTEGER,
	PASSWORD     VARCHAR2(255),
	ADDRESS    VARCHAR2(255),
	IS_VERIFIED   VARCHAR(255),
    USER_IMAGE VARCHAR2(255),
    CREATED_AT DATE
	
);

-- Drop the existing sequence if it exists
DROP SEQUENCE USER_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE USER_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;


 -- Drop the existing trigger if it exists
DROP TRIGGER USER_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER USER_ID_TRIGGER
    BEFORE INSERT ON USER_CLECK
    FOR EACH ROW
BEGIN
    IF :NEW.USER_ID IS NULL THEN
        SELECT USER_ID_SEQ.NEXTVAL INTO :NEW.USER_ID FROM DUAL;
    END IF; 
END;
/

-- Adding  the "USER_ID" into Primary Key
ALTER TABLE USER_CLECK
ADD CONSTRAINT pk_USER_CLECK PRIMARY KEY (USER_ID);

----------------------------------------------------------------------------------------------------------------------------------------------


-- Create a Database table to represent the "SHOP" entity.
CREATE TABLE SHOP (
	SHOP_ID   INTEGER ,
	SHOP_NAME  VARCHAR2(255), 
	SHOP_ADDRESS   VARCHAR2(255), 
	PHONE_NUMBER    INTEGER, 
	SHOP_DESCRIPTION   VARCHAR2(255), 
	TRADER_ID    INTEGER NOT NULL,
    SHOP_ADMIN_VERIFICATION NUMBER,
    SHOP_IMAGE VARCHAR2(255),
    CREATED_AT DATE

);

-- Drop the existing sequence if it exists
DROP SEQUENCE SHOP_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE SHOP_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER SHOP_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER SHOP_ID_TRIGGER
    BEFORE INSERT ON SHOP
    FOR EACH ROW
BEGIN
    IF :NEW.SHOP_ID IS NULL THEN
        SELECT SHOP_ID_SEQ.NEXTVAL INTO :NEW.SHOP_ID FROM DUAL;
    END IF; 
END;
/

-- Adding the SHOP_NAME is unique
-- refering the shop table in Trader so be uniques or Primary
--ALTER TABLE SHOP
--ADD CONSTRAINT uk_SHOP_NAME UNIQUE (SHOP_NAME);

-- Adding  the "USER_ID" into Primary Key
ALTER TABLE SHOP
ADD CONSTRAINT pk_SHOP PRIMARY KEY (SHOP_ID);

-- This constraint ensures that the foreign key of table "SHOP"
-- correctly references the primary key of table "SHOP"
--ALTER TABLE SHOP
--ADD CONSTRAINT fk_SHOP FOREIGN KEY (USER_ID)
--REFERENCES SHOP(SHOP_ID);

ALTER TABLE SHOP
ADD CONSTRAINT fk_SHOP_TRADER_ID FOREIGN KEY (TRADER_ID)
REFERENCES TRADER(TRADER_ID);

---------------------------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------------------------



-- Create a Database table to represent the "PRODUCT" entity.
CREATE TABLE PRODUCT (
	PRODUCT_ID   INTEGER, 
	PRODUCT_NAME    VARCHAR2(255), 
	PRODUCT_PRICE    INTEGER, 
	PRODUCT_QUANTITY  INTEGER, 
	PRODUCT_DESCRIPTION VARCHAR2(255), 
	ALLERGY_INFORMATION   VARCHAR2(255), 
	PRODUCT_IMAGE   VARCHAR2(255), 
	PRODUCT_STOCK  INTEGER, 
	CATEGORY_NAME  VARCHAR2(255), 
    PRODUCT_ADMIN_VERIFICATION NUMBER,
	SHOP_ID  INTEGER,
    USER_ID  INTEGER,
    CREATED_AT DATE

);

-- Drop the existing sequence if it exists
DROP SEQUENCE PRODUCT_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE PRODUCT_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
 -- Drop the existing trigger if it exists
DROP TRIGGER PRODUCT_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER PRODUCT_ID_TRIGGER
    BEFORE INSERT ON PRODUCT
    FOR EACH ROW
BEGIN
    IF :NEW.PRODUCT_ID IS NULL THEN
        SELECT PRODUCT_ID_SEQ.NEXTVAL INTO :NEW.PRODUCT_ID FROM DUAL;
    END IF; 
END;
/

-- Adding  the "PRODUCT_ID" into Primary Key
ALTER TABLE PRODUCT
ADD CONSTRAINT pk_PRODUCT PRIMARY KEY (PRODUCT_ID);


-- This constraint ensures that the foreign key of table "PRODUCT"
-- correctly references the primary key of table "SHOP"
ALTER TABLE PRODUCT
ADD CONSTRAINT fk_WISHLIST_SHOP_ID FOREIGN KEY (SHOP_ID)
REFERENCES SHOP(SHOP_ID);

ALTER TABLE PRODUCT
ADD CONSTRAINT fk_WISHLIST_USER_ID FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);

----------------------------------------------------------------------------------------------------------------------------------------------




-- Create a Database table to represent the "WISHLIST" entity.
CREATE TABLE WISHLIST (
	WISHLIST_ID  INTEGER , 
	WISHLIST_CREATED   DATE, 
	WISHLIST_UPDATE   DATE, 
	WISHLIST_ITEMS   INTEGER, 
	USER_ID        INTEGER 

);

-- Drop the existing sequence if it exists
DROP SEQUENCE WISHLIST_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE WISHLIST_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER WISHLIST_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER WISHLIST_ID_TRIGGER
    BEFORE INSERT ON WISHLIST
    FOR EACH ROW
BEGIN
    IF :NEW.WISHLIST_ID IS NULL THEN
        SELECT WISHLIST_ID_SEQ.NEXTVAL INTO :NEW.WISHLIST_ID FROM DUAL;
    END IF; 
END;
/

-- Adding  the "WISHLIST_ID" into Primary Key
ALTER TABLE WISHLIST
ADD CONSTRAINT pk_WISHLIST PRIMARY KEY (WISHLIST_ID);

-- This constraint ensures that the foreign key of table "WISHLIST"
-- correctly references the primary key of table "WISHLIST"
ALTER TABLE WISHLIST
ADD CONSTRAINT fk_WISHLIST FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);

---------------------------------------------------------------------------------------------------------------------------------------------




-- Create a Database table to represent the "PRODUCT_WISHLIST" entity.
CREATE TABLE PRODUCT_WISHLIST (
	PRODUCT_WISHLIST_ID   INTEGER ,
	WISHLIST_ID  INTEGER NOT NULL, 
	PRODUCT_ID   INTEGER NOT NULL

);


-- Drop the existing sequence if it exists
DROP SEQUENCE PRODUCT_WISHLIST_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE PRODUCT_WISHLIST_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER PRODUCT_WISHLIST_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER PRODUCT_WISHLIST_ID_TRIGGER
    BEFORE INSERT ON PRODUCT_WISHLIST
    FOR EACH ROW
BEGIN
    IF :NEW.PRODUCT_WISHLIST_ID IS NULL THEN
        SELECT PRODUCT_WISHLIST_ID_SEQ.NEXTVAL INTO :NEW.PRODUCT_WISHLIST_ID FROM DUAL;
    END IF; 
END;
/

-- Adding  the "PRODUCT_WISHLIST_ID" into Primary Key
ALTER TABLE PRODUCT_WISHLIST
ADD CONSTRAINT pk_PRODUCT_WISHLIST PRIMARY KEY (PRODUCT_WISHLIST_ID);

-- This constraint ensures that the foreign key of table "PRODUCT_WISHLIST"
-- correctly references the primary key of table "WISHLIST"
ALTER TABLE PRODUCT_WISHLIST
ADD CONSTRAINT fk_PRODUCT_WISHLIST_WISHLIST_ID 
FOREIGN KEY (WISHLIST_ID)
REFERENCES WISHLIST(WISHLIST_ID);

-- This constraint ensures that the foreign key of table "PRODUCT_WISHLIST"
-- correctly references the primary key of table "PRODUCT"
ALTER TABLE PRODUCT_WISHLIST
ADD CONSTRAINT fk_PRODUCT_WISHLIST_PRODUCT_ID 
FOREIGN KEY (PRODUCT_ID)
REFERENCES PRODUCT(PRODUCT_ID);

-------------------------------------------------------------------------------------------------------------------------------------------




-- Create a Database table to represent the "DISCOUNT" entity.
CREATE TABLE DISCOUNT (
	DISCOUNT_ID   INTEGER , 
	DISCOUNT_AMOUNT   INTEGER, 
	DISCOUNT_PERCENT  INTEGER, 
	USER_ID     INTEGER, 
	PRODUCT_ID   INTEGER

);


-- Drop the existing sequence if it exists
DROP SEQUENCE DISCOUNT_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE DISCOUNT_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER DISCOUNT_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER DISCOUNT_ID_TRIGGER
    BEFORE INSERT ON DISCOUNT
    FOR EACH ROW
BEGIN
    IF :NEW.DISCOUNT_ID IS NULL THEN
        SELECT DISCOUNT_ID_SEQ.NEXTVAL INTO :NEW.DISCOUNT_ID FROM DUAL;
    END IF; 
END;
/

-- Adding  the "DISCOUNT_ID" into Primary Key
ALTER TABLE DISCOUNT
ADD CONSTRAINT pk_DISCOUNT PRIMARY KEY (DISCOUNT_ID);

-- This constraint ensures that the foreign key of table "DISCOUNT"
-- correctly references the primary key of table "USER_CLECK"
ALTER TABLE DISCOUNT
ADD CONSTRAINT fk_DISCOUNT_USER_ID FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);

-- This constraint ensures that the foreign key of table "DISCOUNT"
-- correctly references the primary key of table "PRODUCT"
ALTER TABLE DISCOUNT
ADD CONSTRAINT fk_DISCOUNT_PRODUCT_ID FOREIGN KEY (PRODUCT_ID)
REFERENCES PRODUCT(PRODUCT_ID);

------------------------------------------------------------------------------------------------------------------------------------------



-- Create a Database table to represent the "REVIEW" entity.
CREATE TABLE REVIEW (
	REVIEW_ID   INTEGER , 
	REVIEW_DATE   DATE, 
	REVIEW_SCORE   INTEGER, 
	REVIEW_COMMENT   VARCHAR2(255),
	USER_ID    INTEGER, 
	PRODUCT_ID   INTEGER 

);


-- Drop the existing sequence if it exists
DROP SEQUENCE REVIEW_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE REVIEW_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER REVIEW_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER REVIEW_ID_TRIGGER
    BEFORE INSERT ON REVIEW
    FOR EACH ROW
BEGIN
    IF :NEW.REVIEW_ID IS NULL THEN
        SELECT REVIEW_ID_SEQ.NEXTVAL INTO :NEW.REVIEW_ID FROM DUAL;
    END IF; 
END;
/


-- Adding  the "REVIEW_ID" into Primary Key
ALTER TABLE REVIEW
ADD CONSTRAINT pk_REVIEW PRIMARY KEY (REVIEW_ID);

-- This constraint ensures that the foreign key of table "DISCOUNT"
-- correctly references the primary key of table "USER_CLECK"
ALTER TABLE REVIEW
ADD CONSTRAINT fk_REVIEW_USER_ID FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);

-- This constraint ensures that the foreign key of table "DISCOUNT"
-- correctly references the primary key of table "PRODUCT"
ALTER TABLE REVIEW
ADD CONSTRAINT fk_REVIEW_PRODUCT_ID FOREIGN KEY (PRODUCT_ID)
REFERENCES PRODUCT(PRODUCT_ID);

---------------------------------------------------------------------------------------------------------------------------------------------



-- Create a Database table to represent the "CART" entity.
CREATE TABLE CART (
	CART_ID   INTEGER , 
	CART_CREATED  DATE, 
	CART_UPDATED   DATE, 
	CART_ITEMS  INTEGER, 
	USER_ID   INTEGER

);


-- Drop the existing sequence if it exists
DROP SEQUENCE CART_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE CART_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER CART_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER CART_ID_TRIGGER
    BEFORE INSERT ON CART
    FOR EACH ROW
BEGIN
    IF :NEW.CART_ID IS NULL THEN
        SELECT CART_ID_SEQ.NEXTVAL INTO :NEW.CART_ID FROM DUAL;
    END IF; 
END;
/


-- Adding  the "CART_ID" into Primary Key
ALTER TABLE CART
ADD CONSTRAINT pk_CART PRIMARY KEY (CART_ID);

-- This constraint ensures that the foreign key of table "DISCOUNT"
-- correctly references the primary key of table "USER_CLECK"
ALTER TABLE CART
ADD CONSTRAINT fk_CART FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);

------------------------------------------------------------------------------------------------------------------------------------------



-- Create a Database table to represent the "CART_PRODUCT" entity.
CREATE TABLE CART_PRODUCT (
	CART_PRODUCT_ID INTEGER ,
	CART_ID   INTEGER , 
	PRODUCT_ID   INTEGER ,
    QUANTITY INTEGER

);


-- Drop the existing sequence if it exists
DROP SEQUENCE CART_PRODUCT_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE CART_PRODUCT_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER CART_PRODUCT_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER CART_PRODUCT_ID_TRIGGER
    BEFORE INSERT ON CART_PRODUCT
    FOR EACH ROW
BEGIN
    IF :NEW.CART_PRODUCT_ID IS NULL THEN
        SELECT CART_PRODUCT_ID_SEQ.NEXTVAL INTO :NEW.CART_PRODUCT_ID FROM DUAL;
    END IF; 
END;
/


-- Adding  the "CART_ID" into Primary Key
ALTER TABLE CART_PRODUCT
ADD CONSTRAINT pk_CART_PRODUCT PRIMARY KEY (CART_PRODUCT_ID);

-- This constraint ensures that the foreign key of table "CART_PRODUCT"
-- correctly references the primary key of table "CART"
ALTER TABLE CART_PRODUCT
ADD CONSTRAINT fk_CART_PRODUCT_CART_ID FOREIGN KEY (CART_ID)
REFERENCES CART(CART_ID);

-- This constraint ensures that the foreign key of table "CART_PRODUCT"
-- correctly references the primary key of table "PRODUCT"
ALTER TABLE CART_PRODUCT
ADD CONSTRAINT fk_CART_PRODUCT_PRODUCT_ID FOREIGN KEY (PRODUCT_ID)
REFERENCES PRODUCT(PRODUCT_ID);

-------------------------------------------------------------------------------------------------------------------------------------------




-- Create a Database table to represent the "COLLECTION_SLOT" entity.
CREATE TABLE COLLECTION_SLOT (
	COLLECTION_SLOT_ID    INTEGER, 
	COLLECTION_TIME VARCHAR2(255),
	COLLECTION_DATE  DATE

);

-- Drop the existing sequence if it exists
DROP SEQUENCE COLLECTION_SLOT_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE COLLECTION_SLOT_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER COLLECTION_SLOT_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER COLLECTION_SLOT_ID_TRIGGER
    BEFORE INSERT ON COLLECTION_SLOT
    FOR EACH ROW
BEGIN
    IF :NEW.COLLECTION_SLOT_ID IS NULL THEN
        SELECT COLLECTION_SLOT_ID_SEQ.NEXTVAL INTO :NEW.COLLECTION_SLOT_ID FROM DUAL;
    END IF; 
END;
/

-- Adding  the "ORDER_ID" into Primary Key
ALTER TABLE COLLECTION_SLOT
ADD CONSTRAINT pk_COLLECTION_SLOT PRIMARY KEY (COLLECTION_SLOT_ID);

----------------------------------------------------------------------------------------------------------------------------------------------



-- Create a Database table to represent the "ORDERS" entity.
CREATE TABLE ORDERS (
	ORDER_ID   INTEGER , 
	ORDER_QUANTITY   INTEGER, 
	ORDER_DATE TIMESTAMP(6), 
	TOTAL_AMOUNT   INTEGER, 
	INVOICE_NO    INTEGER, 
	COLLECTION_SLOT_ID     INTEGER,
    USER_ID INTEGER,
	CART_ID     INTEGER NOT NULL,
    PRODUCT_ID INTEGER

);



-- Drop the existing sequence if it exists
DROP SEQUENCE ORDER_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE ORDER_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER ORDER_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER ORDER_ID_TRIGGER
    BEFORE INSERT ON ORDERS
    FOR EACH ROW
BEGIN
    IF :NEW.ORDER_ID IS NULL THEN
        SELECT ORDER_ID_SEQ.NEXTVAL INTO :NEW.ORDER_ID FROM DUAL;
    END IF; 
END;
/


-- Adding  the "ORDER_ID" into Primary Key
ALTER TABLE ORDERS
ADD CONSTRAINT pk_ORDERS PRIMARY KEY (ORDER_ID);

-- This constraint ensures that the foreign key of table "ORDERS"
-- correctly references the primary key of table "COLLECTION_SLOT"
ALTER TABLE ORDERS
ADD CONSTRAINT fk_ORDERS_COLLECTION_SLOT_ID FOREIGN KEY (COLLECTION_SLOT_ID)
REFERENCES COLLECTION_SLOT(COLLECTION_SLOT_ID);

-- This constraint ensures that the foreign key of table "ORDERS"
-- correctly references the primary key of table "CART"
ALTER TABLE ORDERS
ADD CONSTRAINT fk_ORDERS_CART_ID FOREIGN KEY (CART_ID)
REFERENCES CART(CART_ID);

ALTER TABLE ORDERS
ADD CONSTRAINT fk_ORDERS_USER_ID FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);


ALTER TABLE ORDERS
ADD CONSTRAINT fk_ORDERS_PRODUCT_ID FOREIGN KEY (PRODUCT_ID)
REFERENCES PRODUCT(PRODUCT_ID);

--------------------------------------------------------------------------------------------------------------------------------------------


-- Create a Database table to represent the "ORDERS_DETAILS" entity.
CREATE TABLE ORDERS_DETAILS (
	ORDER_ID   INTEGER 

);



 -- Drop the existing trigger if it exists
DROP TRIGGER ORDER_DETAILS_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER ORDER_DETAILS_ID_TRIGGER
    BEFORE INSERT ON ORDERS_DETAILS
    FOR EACH ROW
BEGIN
    IF :NEW.ORDER_ID IS NULL THEN
        SELECT ORDER_ID_SEQ.NEXTVAL INTO :NEW.ORDER_ID FROM DUAL;
    END IF; 
END;
/

-- This constraint ensures that the foreign key of table "ORDERS_DETAILS"
-- correctly references the primary key of table "ORDERS"
ALTER TABLE ORDERS_DETAILS
ADD CONSTRAINT fk_ORDERS_DETAILS FOREIGN KEY (ORDER_ID)
REFERENCES ORDERS(ORDER_ID);

---------------------------------------------------------------------------------------------------------------------------------------------




-- Create a Database table to represent the "PAYMENT" entity.
CREATE TABLE PAYMENT (
	PAYMENT_ID   INTEGER , 
	PAYMENT_AMOUNT     INTEGER, 
	PAID_VIA      VARCHAR2(255), 
	PAYMENT_DATE    DATE, 
    PAYMENT_TIME  TIMESTAMP(6),
	USER_ID     INTEGER, 
	ORDER_ID    INTEGER
    

);


-- Drop the existing sequence if it exists
DROP SEQUENCE PAYMENT_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE PAYMENT_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER PAYMENT_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER PAYMENT_ID_TRIGGER
    BEFORE INSERT ON PAYMENT
    FOR EACH ROW
BEGIN
    IF :NEW.PAYMENT_ID IS NULL THEN
        SELECT PAYMENT_ID_SEQ.NEXTVAL INTO :NEW.PAYMENT_ID FROM DUAL;
    END IF; 
END;
/

-- Adding  the "PAYMENT_ID" into Primary Key
ALTER TABLE PAYMENT
ADD CONSTRAINT pk_PAYMENT PRIMARY KEY (PAYMENT_ID);

-- This constraint ensures that the foreign key of table "PAYMENT"
-- correctly references the primary key of table "USER_CLECK"
ALTER TABLE PAYMENT
ADD CONSTRAINT fk_PAYMENT_USER_ID FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);

-- This constraint ensures that the foreign key of table "PAYMENT"
-- correctly references the primary key of table "ORDERS"
ALTER TABLE PAYMENT
ADD CONSTRAINT fk_PAYMENT_ORDER_ID FOREIGN KEY (ORDER_ID)
REFERENCES ORDERS(ORDER_ID);

---------------------------------------------------------------------------------------------------------------------------------------------



-- Create a table Trader
CREATE TABLE TRADER (
	TRADER_ID  INTEGER ,
	TRADER_FIRST_NAME   VARCHAR2(50),
	TRADER_LAST_NAME   VARCHAR2(32),
	TUSER_NAME      VARCHAR2(40),
	EMAIL_ADDRESS   VARCHAR(32),
	TRADER_PASSWORD  VARCHAR2(255),
	TRADER_ADDRESS    VARCHAR2(255),
	CONTACT_NO    INTEGER,
	PRODUCT_CATEGORY  VARCHAR2(60),
	SHOP_NAME  VARCHAR2(255),
	TRADER_IMAGE   VARCHAR2(255),
	IS_VERIFIED   NUMBER,
    TRADER_ADMIN_VERIFICATION NUMBER,
    SHOP_ID INTEGER,
    USER_ID  INTEGER

);



-- Drop the existing sequence if it exists
DROP SEQUENCE TRADER_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE TRADER_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER TRADER_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER TRADER_ID_TRIGGER
    BEFORE INSERT ON TRADER
    FOR EACH ROW
BEGIN
    IF :NEW.TRADER_ID IS NULL THEN
        SELECT TRADER_ID_SEQ.NEXTVAL INTO :NEW.TRADER_ID FROM DUAL;
    END IF; 
END;
/


-- Adding  the "PAYMENT_ID" into Primary Key
ALTER TABLE TRADER
ADD CONSTRAINT pk_TRADER PRIMARY KEY (TRADER_ID);

-- This constraint ensures that the foreign key of table "TRADER"
-- correctly references the primary key of table "SHOP"
ALTER TABLE TRADER
ADD CONSTRAINT fk_TRADER_SHOP_ID FOREIGN KEY (SHOP_ID)
REFERENCES SHOP(SHOP_ID);

ALTER TABLE TRADER
ADD CONSTRAINT fk_TRADER_USER_ID FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);

------------------------------------------------------------------------------------------------------------------------------


-- Create a Review_Access
Create table REVIEW_ACCESS(
    ACCESS_ID INTEGER,
    PRODUCT_ID INTEGER,
    IS_COLLECTED NUMBER,
    USER_ID INTEGER
);



-- Adding  the "REVIEW_ID" into Primary Key
ALTER TABLE REVIEW_ACCESS
ADD CONSTRAINT pk_REVIEW_ACCESS PRIMARY KEY (ACCESS_ID);

-- This constraint ensures that the foreign key of table "PRODUCT"
-- correctly references the primary key of table "SHOP"
ALTER TABLE REVIEW_ACCESS
ADD CONSTRAINT fk_REVIEW_ACCESS_PRODUCT_ID FOREIGN KEY (PRODUCT_ID)
REFERENCES PRODUCT(PRODUCT_ID);


ALTER TABLE REVIEW_ACCESS
ADD CONSTRAINT fk_REVIEW_ACCESS_USER_ID FOREIGN KEY (USER_ID)
REFERENCES USER_CLECK(USER_ID);



-- Drop the existing sequence if it exists
DROP SEQUENCE ACCESS_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE ACCESS_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;

 -- Drop the existing trigger if it exists
DROP TRIGGER ACCESS_ID_TRIGGER;
   -- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER ACCESS_ID_TRIGGER
    BEFORE INSERT ON REVIEW_ACCESS
    FOR EACH ROW
BEGIN
    IF :NEW.ACCESS_ID IS NULL THEN
        SELECT ACCESS_ID_SEQ.NEXTVAL INTO :NEW.ACCESS_ID FROM DUAL;
    END IF; 
END;
/



------------------------------------------------------------------------------------------------------------------------------

CREATE TABLE REVENUE (
    REVENUE_ID          INTEGER PRIMARY KEY,        -- Unique identifier for each record
    REVENUE_DATE        DATE NOT NULL,              -- Date of the revenue record
    TOTAL_SALES         NUMBER(10, 2) NOT NULL,     -- Total sales amount for the day
    TOTAL_ITEMS_SOLD    INTEGER NOT NULL,           -- Total number of items sold
    MOST_SOLD_PRODUCT_ID INTEGER,                   -- Foreign key to the most sold product
    MOST_SOLD_PRODUCT_COUNT INTEGER,                -- Quantity of the most sold product
	TRADER_ID    INTEGER
);

ALTER TABLE REVENUE
ADD CONSTRAINT fk_REVENUE_TRADER_ID FOREIGN KEY (TRADER_ID)
REFERENCES TRADER(TRADER_ID);

-- Drop the existing sequence if it exists
DROP SEQUENCE REVENUE_ID_SEQ;

-- Create the sequence for generating user IDs
CREATE SEQUENCE REVENUE_ID_SEQ
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    NOCACHE;
    
    
 -- Drop the existing trigger if it exists
DROP TRIGGER REVENUE_ID_TRIGGER;

-- Create the trigger to populate the TRADER_ID column with the next value from the sequence
CREATE OR REPLACE TRIGGER REVENUE_ID_TRIGGER
    BEFORE INSERT ON REVENUE
    FOR EACH ROW
BEGIN
    IF :NEW.REVENUE_ID IS NULL THEN
        SELECT REVENUE_ID_SEQ.NEXTVAL INTO :NEW.REVENUE_ID FROM DUAL;
    END IF; 
END;
/