package com.example.employeeapi.service;

import com.example.employeeapi.model.Department;

import java.util.List;
import java.util.Map;

public interface DepartmentService {
    enum OperationType {
        AVG, MAX, MIN
    }
    List<Department> getAllDepartments();
    List<Department> getAllTrashes();
    Department getDepartmentByDnumber(int dnumber);
    List<Department> getDepartmentByAttr(List<String> searchAttr, List<Object> departmentValue);
    boolean soft_deleteDepartmentByDnumber(int dnumber);
    boolean hard_deleteDepartmentByDnumber(int dnumber);
    boolean restoreDepartmentByDnumber(int dnumber);
    Department updateDepartmentByDnumber(int dnumber, Map<String, Object> updateValue);
    Department addDepartment(List<Object> addingValue);
    List<Double> getDepartmentInfo(int dnumber);
}
