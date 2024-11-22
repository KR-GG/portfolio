package com.example.employeeapi.model;

import java.sql.Date;
import java.sql.Timestamp;

public class Employee {
    private String fname;       // First Name
    private String minit;       // Middle Initial
    private String lname;       // Last Name
    private String ssn;         // Social Security Number
    private Date bdate; // Birth Date
    private String address;     // Address
    private String sex;         // Sex
    private double salary;      // Salary
    private String superSsn;    // Supervisor's SSN
    private String dname;           // Department Number
    private Timestamp created;   // Created timestamp
    private Timestamp modified;  // Modified timestamp

    // 주생성자
    public Employee() {
    }

    public Employee(String fname, String minit, String lname, String ssn,
                    Date bdate, String address, String sex,
                    double salary, String superSsn, String dname,
                    Timestamp created, Timestamp modified) {
        this.fname = fname;
        this.minit = minit;
        this.lname = lname;
        this.ssn = ssn;
        this.bdate = bdate;
        this.address = address;
        this.sex = sex;
        this.salary = salary;
        this.superSsn = superSsn;
        this.dname = dname;
        this.created = created;
        this.modified = modified;
    }

    // Getter 및 Setter 메소드
    public String getFname() {
        return fname;
    }

    public void setFname(String fname) {
        this.fname = fname;
    }

    public String getMinit() {
        return minit;
    }

    public void setMinit(String minit) {
        this.minit = minit;
    }

    public String getLname() {
        return lname;
    }

    public void setLname(String lname) {
        this.lname = lname;
    }

    public String getSsn() {
        return ssn;
    }

    public void setSsn(String ssn) {
        this.ssn = ssn;
    }

    public Date getBdate() {
        return bdate;
    }

    public void setBdate(Date bdate) {
        this.bdate = bdate;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getSex() {
        return sex;
    }

    public void setSex(String sex) {
        this.sex = sex;
    }

    public double getSalary() {
        return salary;
    }

    public void setSalary(double salary) {
        this.salary = salary;
    }

    public String getSuperSsn() {
        return superSsn;
    }

    public void setSuperSsn(String superSsn) {
        this.superSsn = superSsn;
    }

    public String getDname() {
        return dname;
    }

    public void setDname(String dname) {
        this.dname = dname;
    }

    public Timestamp getCreated() {
        return created;
    }

    public void setCreated(Timestamp created) {
        this.created = created;
    }

    public Timestamp getModified() {
        return modified;
    }

    public void setModified(Timestamp modified) {
        this.modified = modified;
    }
}
