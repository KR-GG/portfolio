package com.example.employeeapi.model;

import java.util.Date;

public class Department {
    private String Dname;
    private int Dnumber;
    private String Mgr_ssn;
    private Date Mgr_start_date;

    public Department() {
    }

    public Department(String dname, int dnumber, String mgr_ssn, Date mgr_start_date) {
        this.Dname = dname;
        this.Dnumber = dnumber;
        this.Mgr_ssn = mgr_ssn;
        this.Mgr_start_date = mgr_start_date;
    }

    // Getter and Setter for Dname
    public String getDname() {
        return Dname;
    }

    public void setDname(String dname) {
        this.Dname = dname;
    }

    // Getter and Setter for Dnumber
    public int getDnumber() {
        return Dnumber;
    }

    public void setDnumber(int dnumber) {
        this.Dnumber = dnumber;
    }

    // Getter and Setter for Mgr_ssn
    public String getMgr_ssn() {
        return Mgr_ssn;
    }

    public void setMgr_ssn(String mgr_ssn) {
        this.Mgr_ssn = mgr_ssn;
    }

    // Getter and Setter for Mgr_start_date
    public Date getMgr_start_date() {
        return Mgr_start_date;
    }

    public void setMgr_start_date(Date mgr_start_date) {
        this.Mgr_start_date = mgr_start_date;
    }
}
