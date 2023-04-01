import tw from 'twin.macro';
import { breakpoint } from '@/theme';
import * as Icon from 'react-feather';
import { Link } from 'react-router-dom';
import useFlash from '@/plugins/useFlash';
import styled from 'styled-components/macro';
import React, { useState, useEffect } from 'react';
import Spinner from '@/components/elements/Spinner';
import { Button } from '@/components/elements/button';
import { Dialog } from '@/components/elements/dialog';
import { getCosts, Costs } from '@/api/store/getCosts';
import purchaseResource from '@/api/store/purchaseResource';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import PurchaseBox from '@/components/elements/store/PurchaseBox';
import PageContentBlock from '@/components/elements/PageContentBlock';

const Container = styled.div`
    ${tw`flex flex-wrap`};

    & > div {
        ${tw`w-full`};

        ${breakpoint('sm')`
      width: calc(50% - 1rem);
    `}

        ${breakpoint('md')`
      ${tw`w-auto flex-1`};
    `}
    }
`;

export default () => {
    const [open, setOpen] = useState(false);
    const [costs, setCosts] = useState<Costs>();
    const [resource, setResource] = useState('');
    const { addFlash, clearFlashes, clearAndAddHttpError } = useFlash();

    useEffect(() => {
        getCosts().then((costs) => setCosts(costs));
    }, []);

    const purchase = (resource: string) => {
        clearFlashes('store:resources');

        purchaseResource(resource)
            .then(() => {
                setOpen(false);
                addFlash({
                    type: 'success',
                    key: 'store:resources',
                    message: 'Resource has been added to your account.',
                });
            })
            .catch((error) => clearAndAddHttpError({ key: 'store:resources', error }));
    };

    if (!costs) return <Spinner size={'large'} centered />;

    return (
        <PageContentBlock
            title={'Buy Resources'}
            description={'Buy more resources to add to your server.'}
            showFlashKey={'store:resources'}
        >
            <SpinnerOverlay size={'large'} visible={open} />
            <Dialog.Confirm
                open={open}
                onClose={() => setOpen(false)}
                title={'Confirm resource seletion'}
                confirm={'Continue'}
                onConfirmed={() => purchase(resource)}
            >
                Are you sure you want to purchase this resource ({resource})? This will take the credits from your
                account and add the resource. This is not a reversible transaction.
            </Dialog.Confirm>
            <Container className={'j-up lg:grid lg:grid-cols-4 my-10 gap-8'}>
                <PurchaseBox
                    type={'CPU'}
                    amount={50}
                    suffix={'%'}
                    cost={costs.cpu}
                    setOpen={setOpen}
                    icon={<Icon.Cpu />}
                    setResource={setResource}
                    description={'Buy CPU to improve server load times and performance.'}
                />
                <PurchaseBox
                    type={'Memory'}
                    amount={1}
                    suffix={'GB'}
                    cost={costs.memory}
                    setOpen={setOpen}
                    icon={<Icon.PieChart />}
                    setResource={setResource}
                    description={'Buy RAM to improve overall server performance.'}
                />
                <PurchaseBox
                    type={'Disk'}
                    amount={1}
                    suffix={'GB'}
                    cost={costs.disk}
                    setOpen={setOpen}
                    icon={<Icon.HardDrive />}
                    setResource={setResource}
                    description={'Buy disk to store more files.'}
                />
                <PurchaseBox
                    type={'Slots'}
                    amount={1}
                    cost={costs.slots}
                    setOpen={setOpen}
                    icon={<Icon.Server />}
                    setResource={setResource}
                    description={'Buy a server slot so you can deploy a new server.'}
                />
            </Container>
            <Container className={'j-up lg:grid lg:grid-cols-4 my-10 gap-8'}>
                <PurchaseBox
                    type={'Ports'}
                    amount={1}
                    cost={costs.ports}
                    setOpen={setOpen}
                    icon={<Icon.Share2 />}
                    setResource={setResource}
                    description={'Buy a network port to add to a server.'}
                />
                <PurchaseBox
                    type={'Backups'}
                    amount={1}
                    cost={costs.backups}
                    setOpen={setOpen}
                    icon={<Icon.Archive />}
                    setResource={setResource}
                    description={'Buy a backup to keep your data secure.'}
                />
                <PurchaseBox
                    type={'Databases'}
                    amount={1}
                    cost={costs.databases}
                    setOpen={setOpen}
                    icon={<Icon.Database />}
                    setResource={setResource}
                    description={'Buy a database to get and set data.'}
                />
                <TitledGreyBox title={'How to use resources'}>
                    <p className={'font-semibold'}>Adding to an existing server</p>
                    <p className={'text-xs text-gray-500'}>
                        If you have a server that is already deployed, you can add resources to it by going to the
                        &apos;edit&apos; tab.
                    </p>
                    <p className={'font-semibold mt-1'}>Adding to a new server</p>
                    <p className={'text-xs text-gray-500'}>
                        You can buy resources and add them to a new server in the server creation page, which you can
                        access via the store.
                    </p>
                </TitledGreyBox>
            </Container>
            <div className={'flex justify-center items-center'}>
                <div className={'bg-auto bg-center bg-storeone p-4 m-4 rounded-lg'}>
                    <div className={'text-center bg-gray-900 bg-opacity-75 p-4'}>
                        <h1 className={'j-down text-4xl'}>Ready to get started?</h1>
                        <Link to={'/store/create'}>
                            <Button.Text className={'j-up w-full mt-4'}>Create a server</Button.Text>
                        </Link>
                    </div>
                </div>
            </div>
        </PageContentBlock>
    );
};
