import React from 'react';
import AdminLTELayout from '../Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import Breadcrumb from '@/Components/Breadcrumb';


const Dashboard = () => {
    const headWeb = 'Dashboard'
    const linksBreadcrumb = [{ title: 'Home', url: '/' }, { title: headWeb, url: '' }];
    return (
        <AdminLTELayout breadcrumb={<Breadcrumb header={headWeb} links={linksBreadcrumb} />}>
            <Head title={headWeb} />
            <section className="content">
                <div className="row">
                    <div className="col-md-12">
                        <div className="app-content">
                            <div className="container-fluid">
                                <div className="row">
                                    <div className="col-lg-3 col-6">
                                        <div className="small-box bg-info">
                                            <div className="inner">
                                                <h3>150</h3>

                                                <p>New Orders</p>
                                            </div>
                                            <div className="icon">
                                                <i className="ion ion-bag"></i>
                                            </div>
                                            <a href="#" className="small-box-footer">More info <i className="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                    <div className="col-lg-3 col-6">
                                        <div className="small-box bg-success">
                                            <div className="inner">
                                                <h3>53<sup style={{ fontSize: '20px' }}>%</sup></h3>

                                                <p>Bounce Rate</p>
                                            </div>
                                            <div className="icon">
                                                <i className="ion ion-stats-bars"></i>
                                            </div>
                                            <a href="#" className="small-box-footer">More info <i className="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                    <div className="col-lg-3 col-6">
                                        <div className="small-box bg-warning">
                                            <div className="inner">
                                                <h3>44</h3>

                                                <p>User Registrations</p>
                                            </div>
                                            <div className="icon">
                                                <i className="ion ion-person-add"></i>
                                            </div>
                                            <a href="#" className="small-box-footer">More info <i className="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                    <div className="col-lg-3 col-6">
                                        <div className="small-box bg-danger">
                                            <div className="inner">
                                                <h3>65</h3>

                                                <p>Unique Visitors</p>
                                            </div>
                                            <div className="icon">
                                                <i className="ion ion-pie-graph"></i>
                                            </div>
                                            <a href="#" className="small-box-footer">More info <i className="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </AdminLTELayout>
    );
};

export default Dashboard;