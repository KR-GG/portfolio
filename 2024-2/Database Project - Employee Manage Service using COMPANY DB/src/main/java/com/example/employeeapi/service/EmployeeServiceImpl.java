package com.example.employeeapi.service;

import com.example.employeeapi.dao.EmployeeDao;
import com.example.employeeapi.model.Employee;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Map;

@Service
public class EmployeeServiceImpl implements EmployeeService{
    private final EmployeeDao employeeDao;

    @Autowired
    public EmployeeServiceImpl(EmployeeDao employeeDao) {
        this.employeeDao = employeeDao;
    }

    @Override
    public List<Employee> getAllEmployees() {
        return employeeDao.getEmployeeByAttr(List.of("EMPLOYEE.trash"), List.of(false));
    }

    @Override
    public List<Employee> getAllTrashes() { return employeeDao.getEmployeeByAttr(List.of("EMPLOYEE.trash"), List.of(true)); }

    @Override
    public Employee getEmployeeBySsn(String employeeSsn) {
        return employeeDao.getEmployeeBySsn(employeeSsn);
    }

    @Override
    public List<Employee> getEmployeeByAttr(List<String> searchAttr, List<Object> employeeValue) {
        searchAttr.add("EMPLOYEE.trash");
        employeeValue.add(false);
        return employeeDao.getEmployeeByAttr(searchAttr, employeeValue);
    }

    @Override
    public boolean soft_deleteEmployeeBySsn(String employeeSsn) {
        if (employeeDao.getEmployeeBySsn(employeeSsn) == null) {
            return false;
        }
        return employeeDao.updateEmployeeBySsn(employeeSsn, Map.of("trash", true)) == null;
    }

    @Override
    public boolean hard_deleteEmployeeBySsn(String employeeSsn) {
        return employeeDao.deleteEmployeeBySsn(employeeSsn);
    }

    @Override
    public boolean restoreEmployeeBySsn(String employeeSsn) {
        return employeeDao.restoreEmployeeBySsn(employeeSsn) != null;
    }

    @Override
    public Employee updateEmployeeBySsn(String employeeSsn, Map<String, Object> updateValue) {
        return employeeDao.updateEmployeeBySsn(employeeSsn, updateValue);
    }

    @Override
    public Employee addEmployee(List<Object> addingValue) {
        return employeeDao.addEmployee(addingValue);
    }

    @Override
    public List<Map<String, Object>> getGroupInfo(String groupBy) {
        return employeeDao.getGroupInfo(groupBy);
    }
}
