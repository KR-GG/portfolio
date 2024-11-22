package com.example.employeeapi.dao;

import com.example.employeeapi.model.Department;
import com.example.employeeapi.service.DepartmentService;

import java.util.List;
import java.util.Map;

public interface DepartmentDao {
    Department getDepartmentByDnumber(int dnumber);
    List<Department> getDepartmentByAttr(List<String> searchAttr, List<Object> departmentValue);
    boolean deleteDepartmentByDnumber(int dnumber);
    Department updateDepartmentByDnumber(int dnumber, Map<String, Object> updateValue);
    Department restoreDepartmentByDnumber(int dnumber);
    Department addDepartment(List<Object> addingValue);
    Double getDepartmentInfo(int dnumber, DepartmentService.OperationType operationType);
}
