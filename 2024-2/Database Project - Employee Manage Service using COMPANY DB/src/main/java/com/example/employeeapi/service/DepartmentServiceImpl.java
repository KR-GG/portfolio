package com.example.employeeapi.service;

import com.example.employeeapi.dao.DepartmentDaoImpl;
import com.example.employeeapi.model.Department;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import static com.example.employeeapi.service.DepartmentService.OperationType.*;

@Service
public class DepartmentServiceImpl implements DepartmentService{
    
    private final DepartmentDaoImpl departmentDao;

    @Autowired
    public DepartmentServiceImpl(DepartmentDaoImpl departmentDao) {
        this.departmentDao = departmentDao;
    }

    @Override
    public List<Department> getAllDepartments() {
        return departmentDao.getDepartmentByAttr(List.of("trash"), List.of(false));
    }

    @Override
    public List<Department> getAllTrashes() {
        return departmentDao.getDepartmentByAttr(List.of("trash"), List.of(true));
    }

    @Override
    public Department getDepartmentByDnumber(int dnumber) {
        return departmentDao.getDepartmentByDnumber(dnumber);
    }

    @Override
    public List<Department> getDepartmentByAttr(List<String> searchAttr, List<Object> departmentValue) {
        return departmentDao.getDepartmentByAttr(searchAttr, departmentValue);
    }

    @Override
    public boolean soft_deleteDepartmentByDnumber(int dnumber) {
        if (departmentDao.getDepartmentByDnumber(dnumber) == null) {
            return false;
        }
        return departmentDao.updateDepartmentByDnumber(dnumber, Map.of("trash", true)) == null;
    }

    @Override
    public boolean hard_deleteDepartmentByDnumber(int dnumber) {
        return departmentDao.deleteDepartmentByDnumber(dnumber);
    }

    @Override
    public boolean restoreDepartmentByDnumber(int dnumber) {
        return departmentDao.restoreDepartmentByDnumber(dnumber) != null;
    }

    @Override
    public Department updateDepartmentByDnumber(int dnumber, Map<String, Object> updateValue) {
        return departmentDao.updateDepartmentByDnumber(dnumber,updateValue);
    }
    @Override
    public Department addDepartment(List<Object> addingValue) {
        return departmentDao.addDepartment(addingValue);
    }
    @Override
    public List<Double> getDepartmentInfo(int dnumber) {
        List<Double> result = new ArrayList<>();
        result.add(departmentDao.getDepartmentInfo(dnumber, AVG));
        result.add(departmentDao.getDepartmentInfo(dnumber, MAX));
        result.add(departmentDao.getDepartmentInfo(dnumber, MIN));
        return result;
    }
}
