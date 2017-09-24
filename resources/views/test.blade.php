
<h1>For User Login</h1>
<form action="http://cocard-church.dev/api/organization/login" method="post">
Email:<input name="emailphone" value=""><br>
Password:<input name="password" value=""><br>
<input type="hidden" name="json" value="true">
<input type="hidden" name="slug" value="church-alpha">
<input type="hidden" name="id" value="2">
<input type="submit" name="submit" >
</form>
<h1>User Registration </h1>
<form action="http://cocard-church.dev/organization/register" method="post">
slug:<input type="text" name="slug" value="church-alpha"><br>
organization_id:<input type="text"  name="organization_id" value="2"><br>
Firstname:<input type="text" name="first_name" value=""><br>
LastName:<input type="text" name="last_name" value=""><br>
Middlename:<input type="text" name="middle_name" value=""><br>
Address:<input type="text" name="address" value=""><br>
City:<input type="text" name="city" value=""><br>
State:<input type="text" name="state" value=""><br>
Zipcode:<input type="text" name="zipcode" value=""><br>
Birthdate:<input type="text" name="birthdate" value=""><br>
Gender: <select name="gender" value="">
            <option value="Male" >Male</option>
            <option value="Female">Female</option>
        </select><br>
Phone Number:<input type="text" name="phone" value=""><br>
Email Address:<input type="text" name="email" value=""><br>
Password:<input type="text" name="password" value=""><br>
Confirm Password:<input type="text" name="password_confirmation" value=""><br>
<input type="text" name="json" placeholder="json" value="true"><br>
<input type="submit" value="Register"><br>
</form>
<h1>Forgot Password</h1>
<form action="http://cocard-church.dev/password/email" method="post">
<input type="text" name="email" value=""><br>
<input type="text" name="json" value="true"><br>
<input type="submit" value="Send password Reset">
</form>
<h1>Password Reset</h1>
<form action="http://cocard-church.dev/password/reset" method="post">
<input name="email" value=""><br>
<input name="password" value=""><br>
<input name="password_confirmation" value=""> 
<input type="text" name="json" value="true"><br>
<input type="hidden" name="token" value="46e598feb510f6ee6cce29a6d715843631be91f5633e6d155c812e99076a58b4">
<input type="submit" value="Send password Reset">
</form>
<h1>For Add Family Memeber</h1>


<form method="POST" action="http://cocard-church.dev/api/organization/family-member/store/$family_id">
    slug:<input type="text" name="slug" value=""><br>
    family_id:<input type="text"  name="family_id" value=""><br>
    user_id:<input type="text"  name="user_id" value=""> <br>
    Firstname:<input type="text"name="first_name" value=""><br>
    Lastname:<input type="text" name="last_name" value=""><br>
    Middlename:<input type="text" name="middle_name" value=""><br>
    Birthdate:<input type="date" name="birthdate" value=""><br>
    Gender: <select name="gender" value="">
                <option value="Male" >Male</option>
                <option value="Female">Female</option>
            </select><br>
    Allergies:<input type="text" name="allergies" value=""><br>
    Image:<input type="file" name="img" value=""><br>
    Relationship:<input type="text" name="relationship" value=""><br>
    Additional Info:<input type="text"  name="additional_info" value=""><br>
    Child Number:<input type="number"  name="child_number" value=""><br>
    <input type="text" name="json" placeholder="json" value="true"><br>
    <input type="text" name="slug" placeholder="slug" value="church-alpha"><br>
    <input type="submit" name="submit" >
</form>
<h1>For Update Family Memeber</h1>
<form method="POST" action="http://cocard-church.dev/api/organization/family-member/update/8">
    <input type="hidden" name="slug" value="church-alpha">
    <input type="hidden"  name="family_id" value="1">
    <input type="hidden"  name="user_id" value="6"> 
    Firstname:<input type="text"name="first_name" value=""><br>
    Lastname:<input type="text" name="last_name" value=""><br>
    Middlename:<input type="text" name="middle_name" value=""><br>
    Birthdate:<input type="date" name="birthdate" value=""><br>
    Gender: <select name="gender" value="">
                <option value="Male" >Male</option>
                <option value="Female">Female</option>
            </select><br>
    Allergies:<input type="text" name="allergies" value=""><br>
    Image:<input type="file" name="img" value=""><br>
    Relationship:<input type="text" name="relationship" value=""><br>
    Additional Info:<input type="text"  name="additional_info" value=""><br>
    Child Number:<input type="number"  name="child_number" value=""><br>
    <input type="text" name="json" placeholder="json" value="true"><br>
    <input type="text" name="slug" placeholder="slug" value="church-alpha"><br>
    <input type="submit" name="submit" >
</form>
<h1>Add to Cart Donation</h1>
<form action="http://cocard-church.dev/organization/church-alpha/api/user/donate/add-donation" method="post">
Donation Catgory ID:<input name="donation_category_id" value=""><br>
Donation Type:<input name="donation_type" value=""><br>
User Id:<input name="user_id" value=""><br>
Amount:<input name="amount" value=""><br>
<input type="text" name="json" placeholder="json" value="true"><br>
<input type="submit" value="Add to Cart">
</form>
<h1>Add to Cart Event</h1>
<form action="http://cocard-church.dev/organization/church-alpha/api/user/event/add-to-cart" method="post">
Name:<input name="name" value=""><br>
Email:<input name="email" value=""><br>
Qty:<input name="qty" value=""><br>
Slug:<input name="slug" value=""><br>
Event ID:<input name="event_id" value=""><br>
Fee:<input name="fee" value=""><br>
Capacity:<input name="capacity" value=""><br>
Pending:<input name="pending" value=""><br>
Total:<input name="total" value=""><br>
User ID:<input name="user_id" value=""><br>
<input type="text" name="json" placeholder="json" value="true"><br>
<input type="submit" value="Add to Cart">
</form>
<h1>Edit User Profile</h1>
<form action="http://cocard-church.dev/api/organization/update/6" method="post">
First Name:<input type="text" name="first_name" value=""><br>
Middle Name:<input type="text" name="middle_name" value=""><br>
Last Name:<input type="text" name="last_name" value=""><br>
Birthdate:<input type="text" name="birthdate" value=""><br>
Gender:<input type="text" name="gender" value=""><br>
Image:<input type="text" name="image" value=""><br>
Email:<input type="text" name="email" value=""><br>
Password:<input type="text" name="password" value=""><br>
Address:<input type="text" name="address" value=""><br>
City:<input type="text" name="city" value=""><br>
State:<input type="text" name="state" value=""><br>
Zipcode:<input type="text" name="zipcode" value=""><br>
Phone:<input type="text" name="phone" value=""><br>
<input type="text" name="json" placeholder="json" value="true"><br>
<input type="submit" value="Save">
</form>
<h1>Appy Volunteer</h1>
<form action="http://cocard-church.dev/volunteer_apply" method="post">
NAme:<input type="text" name="volunteers[0][name]" value=""><br>
Email:<input type="text" name="volunteers[0][email]" value=""><br>
VGroup<select type="text" name="volunteers[0][volunteer_group_id]">
    <option value="1"></option>
    <option value="2"></option>
    <option value="3"></option>
</select><br>
<input type="hidden" name="event_id" id="event_id" value="3">
<input type="submit" value="Save">
</form>
